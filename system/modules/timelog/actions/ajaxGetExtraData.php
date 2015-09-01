<?php

function ajaxGetExtraData_GET(Web $w) {
	$p = $w->pathMatch("class", "id");
	
	if (empty($p['class']) || empty($p['id'])) {
		return;
	}
	
	$object = $w->Timelog->getObject($p['class'], $p['id']);
	
	if (empty($object->id)) {
		return;
	}
	
	$form_data = $w->callHook("timelog", "type_options_for_" . $p['class'], $object);
	
	if (!empty($form_data[0][0][4])) {
		// We dont want the structure for multiColForm, we want it for a select
		echo Html::select($form_data[0][0][2], $form_data[0][0][4]);
	}
	return;
	
//	if (strtolower($p['object']) !== "task") {
//		return;
//	}
//	
//	$task = null;
//	if (class_exists("Task") || class_exists("TaskService")) {
//		$task = $w->Task->getTask($p['id']);
//		if (empty($task->id)) {
//			return;
//		}
//		
//		$task_type = $w->Task->getTaskTypeObject($task->task_type);
//		$time_types = $task_type->getTimeTypes();
//		
//		$form = [
//			"Additional Details" => [
//				[["Task time", "select", "time_type", null, $time_types]]
//			]
//		];
//		
//		$w->out(Html::multiColForm($form));
//	} else {
//		return;
//	}
}
