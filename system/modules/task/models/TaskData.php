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
	
	public static $_db_table = "task_data";
}
