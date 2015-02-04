<?php

// Overview;
// Define the various Task Groups available to the system
// Define the various Task Types within each group
// Set titles, descriptions, statuses and priorities for each group
// Set titles, descriptions and additional form fields for each task type
// Set flag to allow/disallow closed task to be reopened for each Task Group Type
// This allows <module>.tasks.php file to be created under each module,
// integrating Tasks with Flow modules and leveraging the existing functionality of modules
// Such files are loaded by task.model.php-TaskService->_loadTaskFiles()

////////////////////////////////////////////////////
////		TaskGroupType						////
////////////////////////////////////////////////////

class TaskGroupType_TaskTodo extends TaskGroupType {
	function getTaskGroupTypeDescription() {
		return "This is a TODO list. Use this for assigning any work.";
	}

	function getTaskGroupTypeTitle() {
		return "To Do";
	}

	function getTaskTypeArray() {
		return array("Todo" => "To Do");
	}
	
	function getStatusArray() {
		return array(array("New",false),
			array("Assigned",false),
			array("Wip",false),
			array("Pending",false),
			array("Done",true),
			array("Rejected",true));
	}

	function getTaskPriorityArray() {
		return array("Urgent","Normal","Nice to have");
	}
	
	function getCanTaskGroupReopen() {
		return true;
	}
}



////////////////////////////////////////////////
////		TaskType						////
////////////////////////////////////////////////

class TaskType_Todo extends TaskType {
	function getTaskTypeTitle() {
		return "Todo Item";
	}
	
	function getTaskTypeDescription() {
		return "Use this to assign any task.";
	}

}

class TaskGroupType_SoftwareDevelopment extends TaskGroupType {
	function getTaskGroupTypeDescription() {
		return "Use this for tracking software development tasks.";
	}

	function getTaskGroupTypeTitle() {
		return "Software Development";
	}

	function getTaskTypeArray() {
		return array(
			"Todo"=>"To Do",
			"ProgrammingTicket"=>"Ticket");
	}

	function getStatusArray() {
		return array(array("Idea",false),
			array("On Hold",false),
			array("Backlog",false),
			array("Todo",false),
			array("WIP",false),
			array("Testing",false),
			array("Review",false),
			array("Deploy",false),
			array("Live",true),
			array("Rejected",true),
		);
	}

	function getTaskPriorityArray() {
		return array("Critical","Urgent","Normal","Low");
	}
	function getCanTaskGroupReopen() {
		return true;
	}
	
}

/**
 * 
 * Generic Programming Ticket
 * 
 * Modules can be added via the Lookup table:
 * Type = "<TaskGroupTitle> Modules"
 * 
 * @author admin
 *
 */
class TaskType_ProgrammingTicket extends TaskType {
	function getCanTaskGroupReopen() {
		return true;
	}
	
	function getTaskTypeTitle() {
		return "Dev Ticket";
	}

	function getTaskTypeDescription() {
		return "Use this to report any issue or feature request.";
	}

	function getFieldFormArray(TaskGroup $taskgroup, Task $task = null) {
        $taskdata = null;
        if (!empty($task)) {
            $taskdata = $this->w->Task->getTaskData($task->id);
        }
		return array(
            array($this->getTaskTypeTitle(),"section"),
            array("Ticket Type", "select", "b_or_f", $this->getTaskDataValueForKey($taskdata, "b_or_f"), array("Issue","Feature","Task")),
            array("Identifier", "hidden", "ident", $this->getTaskDataValueForKey($taskdata, "ident")),
		);
	}

    private function getTaskDataValueForKey($taskdata, $key) {
        if (empty($taskdata) || empty($key)) {
            return null;
        }
        
        if (!is_array($taskdata)) {
            return null;
        }
        
        foreach($taskdata as $data) {
            if ($data->data_key == $key) {
                return $data->value;
            }
        }
    }
    
	function on_before_insert(Task $task) {
		// Get REQUEST object instead
		if (!empty($_REQUEST["b_or_f"]) && ($_REQUEST["b_or_f"] =='Issue' || $_REQUEST["b_or_f"]=='Task')) {
			$task->status = "Todo";
		}
	}

	/**
	 * set the task title according to the module selected
	 * 
	 * @see TaskType::on_after_insert()
	 */
	function on_after_insert(Task $task) {
	}
	
	
}
