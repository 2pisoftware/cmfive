<?php

Config::set('task', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'search' => array('Tasks' => "Task"),
    'hooks' => array(
        'core_web',
        'core_dbobject',
        'comment',
        'attachment',
		'timelog',
		'admin'
    ),
    'ical' => array(
        'send' => false
    )
));

// Set form mapping objects
Config::append('form.mapping', [
	'Task', 'TaskGroup'
]);

//========= Properties of Task Type Todo ==================

Config::set('task.TaskType_Todo',array(
	'time-type' => array(__("Ordinary Hours"), __("Overtime"), __("Weekend")),
));

//========= Properties of Taskgroup Type Todo ============

Config::set('task.TaskGroupType_TaskTodo', array(
	'title' => 'To Do',
	'description' => 'This is a TODO list. Use this for assigning any work.',
	'can-task-reopen' => true,
	'tasktypes' => array("Todo" => __("To Do")),
	'statuses' => array(
			array(__("New"), false),
            array(__("Assigned"), false),
            array(__("Wip"), false),
            array(__("Pending"), false),
            array(__("Done"), true), // is closing
            array(__("Rejected"), true)), // is closing
	'priorities' => array(__("Urgent"), __("Normal"), __("Nice to have")),
));

//========= Properties of Task Type Programming Task =================

Config::set('task.TaskType_ProgrammingTicket',array(
	'time-type' => array(__("Ordinary Hours"), __("Overtime"), __("Weekend")),
));

//========= Properties of Taskgroup Type SoftwareDevelopment ==

Config::set('task.TaskGroupType_SoftwareDevelopment', array(
	'title' => 'Software Development',
	'description' => 'Use this for tracking software development tasks.',
	'can-task-reopen' => true,
	'tasktypes' => array(
	    "ProgrammingTicket" => __("Programming Task")),
	'statuses' => array(
		array(__("Idea"), false),
		array(__("On Hold"), false),
		array(__("Backlog"), false),
		array(__("Todo"), false),
		array(__("WIP"), false),
		array(__("Testing"), false),
		array(__("Review"), false),
		array(__("Deploy"), false),
		array(__("Live"), true), // is closing
		array(__("Rejected"), true)), // is closing
	'priorities' => array(__("Urgent"), __("Normal"), __("Nice to have")),
));
