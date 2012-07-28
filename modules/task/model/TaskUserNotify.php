<?php
// defines default Task Notification Matrix as set by Task Group settings
class TaskUserNotify extends DbObject {
	var $user_id;			// user ID
	var $task_id;			// task ID
	var $task_creation;		// notify event = task creation 
	var $task_details;		// notify event = change to task details or data 
	var $task_comments;		// notify event = change to task comment 
	var $time_log;			// notify event = change to time log 
	var $task_documents;	// notify event = change to task documents 
	var $task_pages;		// notify event = change to task pages 
	
	function getDbTableName() {
		return "task_user_notify";
	}
}
