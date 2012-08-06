<?php
/**
 * 
 * An object attached to a specific task
 * (or a task attached to a specific object)
 * 
 * @author carsten
 *
 */
class TaskObject extends DbObject {
	var $task_id; 			// which task this object is attached to
	var $key;				// Task value reference
	var $table_name;		// DB table name of object 
	var $object_id;			// object id

	function getDbTableName() {
		return "task_object";
	}
}
