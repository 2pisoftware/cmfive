<?php

function tasklist_ALL(Web $w) {
	History::add("List Tasks");
    // Look for reset
    $reset = $w->request("reset");
    if (empty($reset)) {
        // Get filter values
        $assignee_id = $w->request("assignee_id");
        $creator_id = $w->request("creator_id");

        $task_group_id = $w->request("task_group_id");
        $task_type = $w->request('task_type');
        $task_priority = $w->request('task_priority');
        $task_status = $w->request('task_status');
        $is_closed = $w->request("is_closed");
        $dt_from = $w->request('dt_from');
        $dt_to = $w->request('dt_to');
    }
    
    // Make the query manually
    $query_object = $w->db->get("task")->leftJoin("task_group");
    
    // We can now make ID queries directly to the task_group table because of left join
    if (!empty($task_group_id)) {
        $query_object->where("task_group.id", $task_group_id);
    }
    
    // Repeat above for everything else
    if (!empty($assignee_id)) {
        $query_object->where("task.assignee_id", $assignee_id);
    }
    if (!empty($creator_id)) {
        // $query_object->where("task.creator_id", $creator_id);
    }
    if (!empty($task_type)) {
        $query_object->where("task.task_type", $task_type);
    }
    if (!empty($task_priority)) {
        $query_object->where("task.priority", $task_priority);
    }
    if (!empty($task_status)) {
        $query_object->where("task.status", $task_status);
    }
//    if (!empty($is_closed)) {
//        $query_object->where("task.is_closed", ((is_null($is_closed) || $is_closed == 0) ? 0 : 1));
//    } else {
//        $query_object->where("task.is_closed", 0);
//    }
    // This part is why we want to make our query manually
    if (!empty($dt_from)) {
        if ($dt_from == "NULL") {
            $query_object->where("task.dt_due", null);
        } else {
            $query_object->where("task.dt_due >= ?", $dt_from);
        }
    }
    if (!empty($dt_to)) {
        if ($dt_to == "NULL") {
            $query_object->where("task.dt_due", null);
        } else {
            $query_object->where("task.dt_due <= ?", $dt_to);
        }
    }
    
    // Standard wheres
    $query_object->where("task.is_deleted", array(0, null))->where("task_group.is_active", 1)->where("task_group.is_deleted", 0);
    
    // Fetch dataset and get model objects for them
    $tasks_result_set = $query_object->fetch_all();
    $task_objects = $w->Task->getObjectsFromRows("Task", $tasks_result_set);
    
	// Filter in or out closed tasks based on given is_closed filter parameter
	if (!empty($task_objects)) {
		$task_objects = array_filter($task_objects, function($task) use ($is_closed) {
			return (is_null($is_closed) || $is_closed == 0) ? !$task->getisTaskClosed() : $task->getisTaskClosed();
		});
	}
	
    $w->ctx("tasks", $task_objects);
    
    // Build the filter and its data
    $taskgroup_data = $w->Task->getTaskGroupDetailsForUser();
    $filter_data = array(
        array("Assignee", "select", "assignee_id", !empty($assignee_id) ? $assignee_id : null, $taskgroup_data["members"]),
        array("Creator", "select", "creator_id", !empty($creator_id) ? $creator_id : null, $taskgroup_data["members"]),
        array("Task Group", "select", "task_group_id", !empty($task_group_id) ? $task_group_id : null, $taskgroup_data["taskgroups"]),
        array("Task Type", "select", "task_type", !empty($task_type) ? $task_type : null, $taskgroup_data["types"]),
        array("Task Priority", "select", "task_priority", !empty($task_priority) ? $task_priority : null, $taskgroup_data["priorities"]),
        array("Task Status", "select", "task_status", !empty($task_status) ? $task_status : null, $taskgroup_data["statuses"]),
        array("Closed", "checkbox", "is_closed", !empty($is_closed) ? $is_closed : null)
    );
    
    $w->ctx("filter_data", $filter_data);
    
    
    // tab: notifications
    // list groups and notification based on my role and permissions
    $line = array(array("Task Group", "Your Role", "Creator", "Assignee", "All Others", ""));
    $user_taskgroup_members = $w->Task->getMemberGroups($w->Auth->user()->id);
    if ($user_taskgroup_members) {
        usort($user_taskgroup_members, array("TaskService", "sortbyRole"));

        foreach ($user_taskgroup_members as $member) {
            $taskgroup = $member->getTaskGroup();
            $value_array = array();
            $notify = $w->Task->getTaskGroupUserNotify($w->Auth->user()->id, $member->task_group_id);
            if ($notify) {
                foreach ($notify as $n) {
                    $value = ($n->value == "0") ? "No" : "Yes";
                    $value_array[$n->role][$n->type] = $value;
                }
            } else {
                $notify = $w->Task->getTaskGroupNotify($member->task_group_id);
                if ($notify) {
                    foreach ($notify as $n) {
                        $value = ($n->value == "0") ? "No" : "Yes";
                        $value_array[$n->role][$n->type] = $value;
                    }
                }
            }

            if ($taskgroup->getCanIView()) {
                $title = $w->Task->getTaskGroupTitleById($member->task_group_id);
                $role = strtolower($member->role);

                $line[] = array(
                    $title,
                    ucfirst($role),
                    @$value_array[$role]["creator"],
                    @$value_array[$role]["assignee"],
                    @$value_array[$role]["other"],
                    Html::box(WEBROOT . "/task/updateusergroupnotify/" . $member->task_group_id, " Edit ", true)
                );
            }
            unset($value_array);
        }
        

        // display list
        $w->ctx("notify", Html::table($line, null, "tablesorter", true));
    }
}
