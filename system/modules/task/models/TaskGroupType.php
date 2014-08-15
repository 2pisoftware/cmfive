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
	 * available stati for tasks in 
	 * this group
	 * 
         * So as a revolutionary update to Tasks, we were challenged with the task
         * of having user definable Task statuses, which makes a lot of sense in
         * a Taskgroup that has requirements constantly changing.
         * 
         * So for this the syntax is:
         *    lookup type => get_class($this) . "_status" (e,g, TaskGroupType_TaskTodo_status)
         *    lookup code => Anything, logically it should be "status"
         *    lookup title => <Name of Status>|<true:false> (Of which the boolean value
         *        after the pipe is whether or not the status is a completion status)
	 */
	function getStatusArray() {
            // Check the lookup table
            $lookup_results = $this->w->Lookup->getLookupByType((get_class($this)) . "_status");
            if (!empty($lookup_results)) {
                $data = array();
                foreach($lookup_results as $lookup) {
                    $data[] = explode("|", $lookup->title);
                }
                return $data;
            }	
	}
	
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
