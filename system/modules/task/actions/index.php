<?php

//////////////////////////////////////////
//          TASK DASHBOARD   		//
//////////////////////////////////////////

function index_ALL(Web $w) {
    $w->Task->navigation($w, "Task Dashboard");
        
    // I want to see:
    //   Number of open tasks assigned to me (out of total open tasks) \/
    //   My Tasks that are overdue (with tasks with no due date)
    //   My tasks with urgent status
    //   Taskgroups that I'm a member of and the amount of tasks in it
    
    $total_tasks = $w->db->get("task")->where("is_deleted", 0)->count();
    $task_rows = $w->db->get("task")->leftJoin("task_group")
			->where("task.assignee_id", $w->Auth->user()->id)
			->where("task.is_deleted", array(0, null))
			->where("task_group.is_active", 1)
			->where("task_group.is_deleted", 0)
			->fetch_all();
    $tasks = !empty($task_rows) ? $w->Task->getObjectsFromRows('Task', $task_rows) : [];
    
    $taskgroups = $w->Task->getTaskGroupsForMember($w->Auth->user()->id);
    
    $count_overdue = 0;
    $count_due_soon = 0;
    $count_no_due_date = 0;
    $count_todo_urgent = 0;
    $count_taskgroup_tasks = 0;
    
    // Task group task count
    if (!empty($taskgroups)) {
        foreach($taskgroups as $taskgroup) {
            $count_taskgroup_tasks += count($taskgroup->getTasks());
        }
    }
    
    // Task breakdown
    if (!empty($tasks)) {
		// Strip out tasks that are already done
		$tasks = array_filter($tasks, function($task) {
			return !$task->getisTaskClosed();
		});
		
		if (!empty($tasks)) {
			foreach($tasks as $task) {
				if (!empty($task->dt_due) && ($task->dt_due < time())) {
					$count_overdue++;
				} else if(!empty($task->dt_due) && ($task->dt2time($task->dt_due) <= (time() + (60 * 60 * 24 * 7)))) {
					$count_due_soon++;
				} else if (empty($task->dt_due)) {
					$count_no_due_date++;
				}
				if (strtolower($task->priority) === "urgent") {
					$count_todo_urgent++;
				}
			}
		}
    }
//    
//    // Time log breakdown
//    $beginning_of_today = strtotime("midnight", time());
//    $time_entries = $w->db->get("timelog")
//            ->where("creator_id", $w->Auth->user()->id)
//            ->where("is_deleted", 0)
//            ->where("dt_start >= ?", $w->Task->time2Dt($beginning_of_today - (60 * 60 * 24 * 14)))
//            ->where("dt_start <= ?", $w->Task->time2Dt(strtotime("tomorrow", $beginning_of_today) - 1))
//            ->orderBy("dt_start DESC")->fetch_all();
//    
//    $time_entry_objects = array();
//    if (!empty($time_entries)) {
//        $time_entries = $w->Task->getObjectsFromRows("TaskTime", $time_entries, true);
//    }
//    
//    $time_total_overall = 0;
//    if (!empty($time_entries)) {
//        foreach($time_entries as $time_entry) {
//            
//            $entry_date = date('d/m', $time_entry->dt_start);
//            if (empty($time_entry_objects[$entry_date])) {
//                $time_entry_objects[$entry_date] = array('entries' => array(), "total" => 0);
//            }
//            
//            $time_total_overall += $time_entry->getDuration();
//            $time_entry_objects[$entry_date]['total'] += $time_entry->getDuration();
//            $time_entry_objects[$entry_date]['entries'][] = $time_entry; 
//        }
//    }
//    
    $w->ctx("taskgroups", !empty($taskgroups) ? $taskgroups : []);
    $w->ctx("tasks", !empty($tasks) ? $tasks : []);
    $w->ctx("total_tasks", $total_tasks);
    $w->ctx("count_overdue", $count_overdue);
    $w->ctx("count_due_soon", $count_due_soon);
    $w->ctx("count_no_due_date", $count_no_due_date);
    $w->ctx("count_todo_urgent", $count_todo_urgent);
    $w->ctx("count_taskgroup_tasks", $count_taskgroup_tasks);
    
//    $w->ctx("time_entries", $time_entry_objects);
//    $w->ctx("time_total_overall", $time_total_overall);
}
