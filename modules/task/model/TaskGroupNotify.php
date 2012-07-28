<?php
// defines default Task Group Notification Matrix as set by OWNER
class TaskGroupNotify extends DbObject {
	var $task_group_id;	// task group ID
	var $role;			// member role: guest|membr|owner
	var $type;			// notify type: creator|assignee|all others
	var $value;			// flag: 0|1
	
	function getDbTableName() {
		return "task_group_notify";
	}
}

