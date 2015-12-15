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
	
	$task_type_form = $task_type->getFieldFormArray($task_group, $task);
//	if (!empty($task_type_form)) {
//		foreach($task_type_form as &$row) {
//			$task_data = $w->Task->getObject("TaskData", array("task_id" => $task->id, "data_key" => $row[2]));
//			if (!empty($task_data)) {
//				$row[3] = $task_data->value;
//			}
//		}
//	}
	
    $w->out(
      json_encode(array(
        Html::form($task_type_form, "/task/edit", null, null, "form_fields_form")
      ))
    );
}