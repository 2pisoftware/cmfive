<?php
class TaskTime extends  DbObject {
	public $task_id;
	public $creator_id;	// who created this time entry
	public $dt_created;	// when this time entry was created
	public $user_id;		// who accrued this time (most often == creator_id, but not necessarily!)
	public $dt_start;		// start of time period
	public $dt_end;		// end of time period
	public $comment_id; 	// id of comment associated with this log entry
	public $is_suspect;	// suspect/accept toggle
	public $is_deleted;	// is deleted flag
	
	// actual table name
	function getDbTableName() {
		return "task_time";
	}
}
