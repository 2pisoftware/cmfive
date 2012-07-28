<?php
// defines default Task Group Notification Matrix as set by OWNER
class TaskGroupUserNotify extends DbObject {
	var $user_id;			// user ID
	var $task_group_id;		// task group ID
	var $role;				// member role: guest|membr|owner
	var $type;				// notify type: creator|assignee|all others
	var $value;				// flag: 0|1
	var $task_creation;		// notify event = task creation 
	var $task_details;		// notify event = change to task details or data 
	var $task_comments;		// notify event = change to task comment 
	var $time_log;			// notify event = change to time log 
	var $task_documents;	// notify event = change to task documents 
	var $task_pages;		// notify event = change to task pages 
	
	function getDbTableName() {
		return "task_group_user_notify";
	}
}
