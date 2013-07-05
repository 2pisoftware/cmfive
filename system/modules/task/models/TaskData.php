<?php
/**
 * Store the data of a single task
 * @author carsten
 *
 */
class TaskData extends DbObject {
	var $task_id;
	var $key;
	var $value;	
	
	function getDbTableName() {
		return "task_data";
	}
}
