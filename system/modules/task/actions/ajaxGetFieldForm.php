<?php

function ajaxGetFieldForm_ALL(Web $w) {
    $p = $w->pathMatch("task_type", "task_group_id", "task_id");
    
    if (empty($p['task_group_id']) || empty($p['task_type'])) {
        return;
    }
    
    $task_type = $w->Task->getTaskTypeObject($p['task_type']);
    if (empty($task_type)) {
        return;
    }
    
    $task_group = $w->Task->getTaskgroup($p['task_group_id']);
    if (empty($task_group->id)) {
        return;
    }
  
    $task = null;
    if (!empty($p['task_id'])) {
        $task = $w->Task->getTask($p['task_id']);
    }
    
    $w->out(
      json_encode(array(
        Html::form($task_type->getFieldFormArray($task_group, $task), "/task/edit", null, null, "form_fields_form")
      ))
    );
}