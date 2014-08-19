<?php

function tasklist_ALL(Web $w) {
    $w->Task->navigation($w, "");

    // tab: tasks
    // prepare default filter dropdowns
    // get WHO to return relevant tasks:
    //		a selected assignee, a blank assignee = all assignee's, no assignee = tasks assigned to me
    $assignee_id = (!array_key_exists("assignee", $_REQUEST)) ? $w->Auth->user()->id : $w->request('assignee');

    // for those groups of which i am a member, get list of all members for display in Assignee & Creator dropdowns
    $mygroups = $w->Task->getMemberGroups($w->Auth->user()->id);

    if ($mygroups) {
        foreach ($mygroups as $mygroup) {
            $mymembers = $w->Task->getMembersInGroup($mygroup->task_group_id);
            foreach ($mymembers as $mymem) {
                $members[$mymem[1]] = array($mymem[0], $mymem[1]);
            }
        }
        sort($members);
    }

    // change filter dropdowns to show selectedIndex for current search
//    $w->ctx("reqTaskgroups", $w->request('taskgroups'));
//    $w->ctx("reqTasktypes", $w->request('tasktypes'));
//    $w->ctx("reqPriority", $w->request('tpriority'));
//    $w->ctx("reqStatus", $w->request('status'));
//    $w->ctx("reqdtFrom", $w->request('dt_from'));
//    $w->ctx("reqdtTo", $w->request('dt_to'));

    // prepare WHERE clause as string
    $where = array();
    if ($w->request('task_group_id') != "") {
        $where["task_group_id"] = $w->request('task_group_id');
    }
    if ($w->request('task_type') != "") {
        $where["task_type"] = $w->request('task_type');
    }
    if ($w->request('priority') != "") {
        $where["priority"] = $w->request('priority');
    }
    if (($w->request('status') != "")) {
        $where["status"] = $w->request('status');
    }
    $is_closed = $w->request("is_closed");
    $where["is_closed"] = 0;
    if (!is_null($is_closed)) {
        $where["is_closed"] = intval($is_closed);
    }
    $dt_from = $w->request('dt_from');
    $dt_to = $w->request('dt_to');
    if (!empty($dt_from)) {
        $where["dt_due >= ?"] = $w->Task->date2db($w->request('dt_from'));
    }
    if (!empty($dt_to)) {
        $where["dt_due <= ?"] = $w->Task->date2db($w->request('dt_to'));
    }

    // either use sql join to object_modified, if searching for tasks 'created by' or getObjects for all other searches
    $creator_id = $w->request("creator_id");
    if (!empty($creator_id)) {
        $where["creator_id"] = $creator_id;
    }
    if (!empty($assignee_id)) {
        $where["assignee_id"] = $assignee_id;
    }
    
    $tasks = $w->Task->getTasks($where);
    
    // create task list heading
    $hds = array(array("Title", "Assigned To", "Group", "Type", "Priority", "Created By", "Status", "Due"));

    // Arrays for filter data
    $taskgroups = array();
    $tasktypes = array();
    $tpriority = array();
    $status_array = array();
    
    // show all tasks found
    if ($tasks) {
        usort($tasks, array("TaskService", "sortTasksbyDue"));
        foreach ($tasks as $task) {
            $task_status = $task->_taskgroup->getStatus();
            if (!in_array($task_status[0], $status_array)) {
                $status_array = array_merge($status_array, $task_status);
            }
            
            // if i can edit the task, allow me to edit the status from the Task List
            if ($task->getCanIEdit() && (!$task->getisTaskClosed() || $task->_taskgroup->getTaskReopen())) {
                $taskstatus = Html::select("status_" . $task->id, $task->getTaskGroupStatus(), $task->status);
            } else {
                $taskstatus = $task->status;
            }

            $thisline = array(
                Html::a(WEBROOT . "/task/edit/" . $task->id, $task->title),
                $w->Task->getUserById($task->assignee_id),
                $task->getTaskGroupTypeTitle(),
                $task->getTypeTitle(),
                $task->priority,
                $task->getTaskCreatorName(),
                $taskstatus,
                $task->isTaskLate()
            );
            $line[] = $thisline;
        }
    }

    // if no tasks found, say as much
    if (empty($line))
        $line = array(array("No Tasks found.", "", "", "", "", "", "", "", ""));

    $line = array_merge($hds, $line);

    // if logged in user is owner of current group, display button to edit the task group
    $btnedit = Html::b("/task-group/viewmembergroup/" . $w->request('taskgroups'), " Edit Task Group ");
    $grpedit = ($w->request('taskgroups') != "") && ($w->Task->getIsOwner($w->request('taskgroups'), $_SESSION['user_id'])) ? $btnedit : "";
    $w->ctx("grpedit", $grpedit);

    // display task list
    $w->ctx("mytasks", Html::table($line, null, "tablesorter", true));
    
    // Sort and remove duplicate values from the status filter array
    asort($status_array);
    $unique_status_array = array();
    foreach ($status_array as $stat_array) {
        $unique_status_array[$stat_array[0]] = $stat_array[0];
    }
    
    $filter_data = array(
        array("Assignee", "select", "assignee_id", $assignee_id, !empty($members) ? $members : null),
        array("Creator", "select", "creator_id", $w->request('creator'), !empty($members) ? $members : null),
        array("Taskgroup", "select", "task_group_id", $w->request('taskgroups'), $taskgroups),
        array("Task Type", "select", "task_type", $w->request('tasktypes'), $tasktypes),
        array("Task Priority", "select", "priority", $w->request('tpriority'), $tpriority),
        array("Status", "select", "status", $w->request('status'), $unique_status_array),
        array("Closed", "checkbox", "is_closed", $w->request('closed'))
    );
    $w->ctx("filter_data", $filter_data);
    
    
    // tab: notifications
    // list groups and notification based on my role and permissions
    $line = array(array("Task Group", "Your Role", "Creator", "Assignee", "All Others", ""));

    if ($mygroups) {
        usort($mygroups, array("TaskService", "sortbyRole"));

        foreach ($mygroups as $mygroup) {
            $taskgroup = $w->Task->getTaskGroup($mygroup->task_group_id);
            $caniview = $taskgroup->getCanIView();

            $notify = $w->Task->getTaskGroupUserNotify($_SESSION['user_id'], $mygroup->task_group_id);
            if ($notify) {
                foreach ($notify as $n) {
                    $value = ($n->value == "0") ? "No" : "Yes";
                    $v[$n->role][$n->type] = $value;
                }
            } else {
                $notify = $w->Task->getTaskGroupNotify($mygroup->task_group_id);
                if ($notify) {
                    foreach ($notify as $n) {
                        $value = ($n->value == "0") ? "No" : "Yes";
                        $v[$n->role][$n->type] = $value;
                    }
                }
            }

            if ($caniview) {
                $title = $w->Task->getTaskGroupTitleById($mygroup->task_group_id);
                $role = strtolower($mygroup->role);

                $line[] = array(
                    $title,
                    ucfirst($role),
                    $v[$role]["creator"],
                    $v[$role]["assignee"],
                    $v[$role]["other"],
                    Html::box(WEBROOT . "/task/updateusergroupnotify/" . $mygroup->task_group_id, " Edit ", true)
                );
            }
            unset($v);
        }
        

        // display list
        $w->ctx("notify", Html::table($line, null, "tablesorter", true));
    }
}
