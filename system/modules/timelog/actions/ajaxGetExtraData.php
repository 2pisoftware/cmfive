<?php

function ajaxGetExtraData_GET(Web $w) {
	$p = $w->pathMatch("id");
	
	if (empty($p['id'])) {
		return;
	}
	
	$timelog = $w->Timelog->getTimelog($p['id']);
	if (empty($timelog)) {
		return;
	}
	
	$form_data = $w->callHook("timelog", "extra_form_fields", $timelog);
	if (!empty($form_data[0])) {
		$w->out(Html::multiColForm($form_data));
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
