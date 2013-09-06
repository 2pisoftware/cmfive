<?php
/**
 * Abstract class describing types of
 * Tasks
 *
 */
abstract class TaskType {
	public $w;
	
	function __construct(Web $w) {
		$this->w = $w;
	}
	function getTaskTypeTitle(){}
	function getTaskTypeDescription() {}
	
	/**
	 * return an array similar to the Html::form
	 * which describes the fields available for this
	 * task type and the way they should be presented in
	 * task details.
	 * 
	 */
	function getFieldFormArray(TaskGroup $taskgroup) {}
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
	/**
	 * Return a html string which will be displayed alongside
	 * the generic task details.
	 * 
	 * @param Task $task
	 */
	function displayExtraDetails(Task $task) {}
	
}
