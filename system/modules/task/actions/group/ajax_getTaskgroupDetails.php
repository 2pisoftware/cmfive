<?php

use Html\Form\Select as Select;

function ajax_getTaskgroupDetails_GET(Web $w) {
	
	list($taskgroup_id) = $w->pathMatch();
	
	$taskgroup = $w->Task->getTaskGroup($taskgroup_id);
	$taskgroup_type = $taskgroup->getTaskGroupTypeObject();
	
	// Normalise taskgroup type fields to be consumed by the Options class
	$task_type_options_array = [];
	$task_type_array = $taskgroup_type->getTaskTypeArray();
	if (!empty($task_type_array)) {
		foreach($task_type_array as $task_type_key => $task_type_value) {
			$task_type_options_array[] = ["label" => $task_type_value, "value" => $task_type_key];
		}
	}
	
	// Build response data
	echo json_encode([
		"taskgroup_type" => $taskgroup->task_group_type,
		"task_types" => (new Select([
			"name" => "new_task_type",
			"id" => "new_task_type"
		]))->setOptions($task_type_options_array)->__toString(),
		"statuses" => (new Select([
			"name" => "new_task_type",
			"id" => "new_task_type"
		]))->setOptions($taskgroup_type->getStatusArray())->__toString(),
		"taskgroup_description" => (new Select([
			"name" => "new_task_type",
			"id" => "new_task_type"
		]))->setOptions($taskgroup_type->getTaskGroupTypeDescription())->__toString(),
		"priorities" => (new Select([
			"name" => "new_task_type",
			"id" => "new_task_type"
		]))->setOptions($taskgroup_type->getTaskPriorityArray())->__toString()
	]);
	
}