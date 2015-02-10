<?php
/**
 * Defines the different types of tasks which 
 * can be assigned in a Task group.
 * 
 * @author carsten
 *
 */
abstract class TaskGroupType {
	public $w;
	
	function __construct(Web $w){
		$this->w = $w;	
	}
	
	/**
	 * Returns the title for the type
	 * 
	 */
	function getTaskGroupTypeTitle() {
	}
	
	function getCanTaskGroupReopen() {
		return false;
	}
	
	/**
	 * Return the description for this type
	 * 
	 */
	function getTaskGroupTypeDescription() {
		
	}
	
	/**
	 * Return array of php class names of concrete
	 * implementations of abstract TaskType
	 * 
	 */
	function getTaskTypeArray() {
	}
	
	/**
	 * Return array containing all
	 * available statuses for tasks in 
	 * this group
	 * 
         
	 */
        function getStatusArray() {}
	
	/**
	 * Return array of all available
	 * priorities in this group
	 * 
	 */
	function getPriorityArray() {
		
	}

	/**
	 * Return array of task permissions
	 * 
	 */
	function getPermissionsArray() {
	}
	
	/**
	 * By default returns the very first status of the
	 * status array if defined. Otherwise "".
	 * @deprecated use getDefaultStatus instead
	 */
	function get_default_status() {
		return $this->getDefaultStatus();
	}	
	
	/**
	 * By default returns the very first status of the
	 * status array if defined. Otherwise "".
	 */
	function getDefaultStatus() {
		$statusarray = $this->getStatusArray();
		if (!empty($statusarray) && sizeof($statusarray) > 0) {
			return $statusarray[0][0];
		} else {
			return "";
		}
	}
	/**
	 * Executed before a task is inserted into DB
	 *
	 * @param Task $task
	 */
	function on_before_insert(Task $task) {}
	/**
	 * Executed after a task has been inserted into DB
	 *
	 * @param Task $task
	 */
	function on_after_insert(Task $task) {}
	/**
	 * Executed before a task is updated in the DB
	 *
	 * @param Task $task
	 */
	function on_before_update(Task $task) {}
	/**
	 * Executed after a task has been updated in the DB
	 *
	 * @param Task $task
	 */
	function on_after_update(Task $task) {}
	/**
	 * Executed before a task is deleted from the DB
	 *
	 * @param Task $task
	 */
	function on_before_delete(Task $task) {}
	/**
	 * Executed after a task has been deleted from the DB
	 *
	 * @param Task $task
	 */
	function on_after_delete(Task $task) {}
	
}
