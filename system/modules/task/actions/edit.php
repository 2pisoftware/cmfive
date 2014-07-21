<?php

function edit_GET($w) {
    $p = $w->pathMatch("id");
    $task = (!empty($p["id"]) ? $w->Task->getTask($p["id"]) : new Task($w));
    
    // Get a list of the taskgroups and filter by what can be used
    $taskgroups = array_filter($w->Task->getTaskGroups(), function($taskgroup){
        return $taskgroup->getCanICreate();
    });
    
    $tasktypes = array();
    $priority = array();
    $members = array();
    
    // Try and prefetch the taskgroup by given id
    $taskgroup_id = $w->request("gid");
    if (!empty($taskgroup_id) or !empty($task->task_group_id)) {
        $taskgroup = $w->Task->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);
        
        if (!empty($taskgroup->id)) {
            $tasktypes = $w->Task->getTaskTypes($taskgroup->task_group_type);
            $priority = $w->Task->getTaskPriority($taskgroup->task_group_type);
            $members = $w->Task->getMembersBeAssigned($taskgroup->id);
            sort($members);
        }
    }
    
    // Create form
    $form = array(
        (!empty($p["id"]) ? "Edit" : "Create") . " a New Task" => array(
            array(array("Task Group", "select", "task_group_id", !empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id, $taskgroups)),
            array(
                array("Task Title", "text", "title", $task->title),
                array("Task Type", "select", "task_type", $task->task_type, $tasktypes)
            ),
            array(
                array("Priority", "select", "priority", $task->priority, $priority),
                array("Date Due", "date", "dt_due", formatDate($task->dt_due))
            ),
            array(array("Description", "textarea", "description", $task->description)),
            array(array("Assigned To", "select", "first_assignee_id", $task->first_assignee_id, $members)),
        )
    );
    $w->ctx("task", $task);
    $w->ctx("form", Html::multiColForm($form, $w->localUrl("/task/edit/{$task->id}"), "POST", "Save", "edit_form"));
}

function edit_POST($w) {
    $p = $w->pathMatch("id");
    $task = (!empty($p["id"]) ? $w->Task->getTask($p["id"]) : new Task($w));
    $taskdata = null;
    if (!empty($p["id"])) {
        $taskdata = $w->Task->getTaskData($p['id']);
    }
    
    $task->fill($_POST);
    if (empty($task->dt_due)) {
        $task->dt_due = $w->Task->getNextMonth();
    }
    
    $response = $task->insertOrUpdate();
//    if (!empty($task->id)) {
//        foreach ($_POST as $name => $value) {
//            if (($name != "formone") && ($name != "FLOW_SID") && ($name != "task_id") && ($name !== CSRF::getTokenID())) {
//                $tdata = new TaskData($w);
//                $arr = array("task_id"=>$task->id,"key"=>$name,"value"=>$value);
//                $tdata->fill($arr);
//                $tdata->insert();
//                unset($arr);
//            }
//        }
//    }
    
    $w->msg("Task " . (!empty($p['id']) ? "updated" : "created"), "/task/viewtask/{$task->id}");
}
