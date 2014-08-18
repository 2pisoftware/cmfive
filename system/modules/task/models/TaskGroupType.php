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
	 * 
	 */
	function get_default_status() {
		if ($this->getStatusArray() && sizeof($this->getStatusArray())) {
			$ar = $this->getStatusArray();
			return $ar[0][0];
		} else {
			return "";
		}
	}	
	
}
