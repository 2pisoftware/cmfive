<?php
/**
 * Store the data of a single task
 * @author carsten
 *
 */
class TaskData extends DbObject {
	public $task_id;
	public $key;
	public $value;	
	
	function getDbTableName() {
		return "task_data";
	}
}
