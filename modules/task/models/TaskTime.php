<?php
class TaskTime extends  DbObject {
	var $task_id;
	var $creator_id;	// who created this time entry
	var $dt_created;	// when this time entry was created
	var $user_id;		// who accrued this time (most often == creator_id, but not necessarily!)
	var $dt_start;		// start of time period
	var $dt_end;		// end of time period
	var $comment_id; 	// id of comment associated with this log entry
	var $is_suspect;	// suspect/accept toggle
	var $is_deleted;	// is deleted flag
	
	// actual table name
	function getDbTableName() {
		return "task_time";
	}
}
