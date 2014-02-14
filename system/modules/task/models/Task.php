<?php
// defines tasks. tasks are associated with a task group and have various attributes
// such as status, priority and current assignee, etc
class Task extends DbObject {
	public $parent_id;			// Parent Task ID.
	public $title;				// not null
	public $task_group_id; 	// can be null!
	public $status;			// text
	public $priority;			// text
	public $task_type;			// text
	public $assignee_id;		// who is currently assigned
	public $dt_assigned;		// date & time of current (last) assignment
	public $dt_first_assigned;	// date & time when first assigned
	public $first_assignee_id;	// who it was assigned to first
	public $dt_completed;		// date & time when completed
	public $is_closed;			// is 1 if this task is closed
	public $dt_planned;		// date & time planned
	public $dt_due;			// date & time due
	public $estimate_hours;	// number of hours estimated
	public $description;
	public $latitude;			
	public $longitude;
	public $is_deleted;		// is_deleted flag
	public $_modifiable;		// Modifiable Aspect
	public $_searchable;
	
	// TODO add TaskData and TaskComments
	function addToIndex() {
		
	}

	
	public static $_db_table = "task";

	/**
	 * Return a html string which will be displayed alongside
	 * the generic task details.
	 * 
	 */	
	function displayExtraDetails() {
		$ttype = $this->getTaskTypeObject();
		if ($ttype) {
			return $ttype->displayExtraDetails($this);
		}
	}	
	
	/** 
	 * return the value of task data given the task ID and the key/name of the target attribute
	 * task data is associated with the additional form fields available to a task type
	 */
	function getDataValue($key) {
		if ($this->id){
			$c = $this->Task->getObject("TaskData",array("task_id"=>$this->id,"key"=>$key));
			if ($c) {
				return $c->value;
			}
		}
	}
	
	/**
	 * 
	 * Set an extra data value field
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	function setDataValue($key,$value) {
		if ($this->id){
			$c = $this->Task->getObject("TaskData",array("task_id"=>$this->id,"key"=>$key));
			if ($c) {
				$c->value = $value;
				$c->update();
			} else {
				$c = new TaskData($this->w);
				$c->key = $key;
				$c->value = $value;
				$c->task_id = $this->id;
				$c->insert();
			}
		}
	}
	
	// get my membership object and compare my role with that required to view tasks given a task group ID
	function getCanIView() {
		$me = $this->Task->getMemberGroupById($this->task_group_id, $_SESSION['user_id']);
		$group = $this->Task->getTaskGroup($this->task_group_id);

		return ($_SESSION['user_id'] == $this->getTaskCreatorId()) ? true : $this->Task->getMyPerms($me->role, $group->can_view);
	}
	
	/**
	 * Used by the search interface
	 * @see DbObject::canView()
	 */
	function canView(User $user) {
		return $this->getCanIView($user);
	}
	
	/**
	 * Used by the search interface
	 * @see DbObject::canList()
	 */
	function canList(User $user) {
		return $this->getCanIView();
	}
	
	// get my membership object and check i am better than GUEST of a task group given a task group ID
	function getCanIEdit() {
		if (($_SESSION['user_id'] == $this->assignee_id) || ($_SESSION['user_id'] == $this->getTaskCreatorId()))
				return true;
	}
	
	// get my membership object and compare my role with that required to assigne tasks given a task group ID
	function getCanIAssign() {
		$me = $this->Task->getMemberGroupById($this->task_group_id, $_SESSION['user_id']);
		$group = $this->Task->getTaskGroup($this->task_group_id);
	
		return $this->Task->getMyPerms($me->role, $group->can_assign); 
	}

	// if i am assignee, creator or task group owner, i can set notifications for this Task
	function getCanINotify() {
		$me = $this->Task->getMemberGroupById($this->task_group_id, $_SESSION['user_id']);
		
		if (($_SESSION['user_id'] == $this->assignee_id) || ($_SESSION['user_id'] == $this->getTaskCreatorId()) || ($this->Task->getMyPerms($me->role, "OWNER")))
				return true;
	}	
		
	// return the ID of the task creator given a task ID
	function getTaskCreatorId() {
		$c = $this->Task->getObject("ObjectModification",array("object_id"=>$this->id,"table_name"=>$this->getDbTableName()));
		return $c ? $c->creator_id : "";
		}

	// return the name for display of the task creator given a task ID
	function getTaskCreatorName() {
		$c = $this->Task->getObject("ObjectModification",array("object_id"=>$this->id,"table_name"=>$this->getDbTableName()));
		$creator = $this->w->Auth->getUser($c->creator_id);
		return $creator ? $creator->getFullName() : "";
		}

	// return the task group title given a task group type
	function getTypeTitle() {
		$c = $this->Task->getTaskTypeObject($this->task_type);
		return $c ? $c->getTaskTypeTitle() : "unknown";
	}
	
	// return the task group description given the task group type
	function getTypeDescription() {
		$c = $this->Task->getTaskTypeObject($this->task_type);
		return $c ? $c->getTaskTypeDescription() : "unknown";
	}
	
	// return the task group title given a task group ID
	function getTaskGroupTypeTitle() {
		$c = $this->Task->getTaskGroup($this->task_group_id);
		return $c ? $c->title : "unknown";
	}
	
	// return the task types as array for a task group given a task group ID
	// return 'unknown' if unknown
	function getTaskGroupTypes() {
		$c = $this->Task->getTaskTypes($this->Task->getTaskGroupTypeById($this->task_group_id));
		return $c ? $c : array("unknown","unknown");
	}
	
	// return the task statuses as array for a task group given a task group ID
	// return 'unknown' if unknown
	function getTaskGroupStatus() {
		$c = $this->Task->getTaskTypeStatus($this->Task->getTaskGroupTypeById($this->task_group_id));
		return $c ? $c : array("unknown","unknown");
	}

	// status array has the form array(<status>,true|false);
	// get status types for a task group given a task group ID
	// given a status, return true| false ... $c[<status>] = true|false
	function getisTaskClosed() {
		$statlist = $this->Task->getTaskStatus($this->Task->getTaskGroupTypeById($this->task_group_id));
		if ($statlist) {
			foreach ($statlist as $stat) {
				$status[$stat[0]] = $stat[1]; 
			}
			return $status[$this->status];
		}
	}
	
	// return the task priorities as array given a task group ID
	function getTaskGroupPriority() {
		$c = $this->Task->getTaskPriority($this->Task->getTaskGroupTypeById($this->task_group_id));
		return $c ? $c : array("unknown","unknown");
	}

	// helper funtion: return all task comments given a task ID
	function getComments($id) {
		return $this->w->Auth->getObjects("AComment",array("obj_table"=>Task::getDbTableName(),"obj_id"=>$id));
	}

	// return the number of comments for a task given a task ID
	function countTaskComments() {
		$comments = $this->getComments($this->id);
		return $comments ? count($comments) : "0";
	}
	
	// return the list of comments, sorted by created date, for a task given a task ID
	function getTaskComments()	{
		$commArray = $this->getComments($this->id);
		
		if($commArray) {
			usort($commArray, array("TaskService", "sortByCreated"));
		}
		return $commArray; 
	}

	// helper function: return all documents attachmented to a task given a task ID
	function getDocos($id) {
		return $this->w->File->getAttachments(Task::getDbTableName(),$id);
	}
    
	// return the number of documents attached to a task
	function countTaskDocos() {
		$docos = $this->getDocos($this->id);
		return $docos ? count($docos) : "0";
	}

    	
	// return the list of documents, sorted by date created/uploaded, attached to a task given a task ID
	function getTaskDocos() {
		$docoArray = $this->getDocos($this->id);

		if ($docoArray)
			usort($docoArray, array("TaskService", "sortByCreated"));

		return $docoArray; 
	}
	
	// return list of time log entries for a task given task ID
	function getTimeLogEntries($id = null) {
            if (empty($id)) {
                $id = $this->id;
            }
            return $this->getObjects("TaskTime",array("task_id"=>$id,"is_deleted"=>0));
	}
	
	// return list of task time log entries, sort by start date
	function getTimeLog() {
		$timelog = $this->getTimeLogEntries($this->id);
		
		if ($timelog)
			usort($timelog, array("TaskService", "sortByStarted"));
			
		return $timelog;
	}
	
	// return due date in bold red for display, if it is on or past the due date
	function isTaskLate() {
		if (($this->dt_due == "0000-00-00 00:00:00") || ($this->dt_due == ""))
			return "Not given";
			
		if ((!$this->getisTaskClosed()) && (date("U") > $this->dt_due)) {
			return "<font color=red><b>" . formatDateTime($this->dt_due) . "</b></font>";
		}
		else {
			return formatDateTime($this->dt_due);
		}
	}
	
	// <module>.tasks.php defines whether tasks in a given task group can be reopened
	function getTaskReopen() {
		if ($this->task_group_id)
			return $this->Task->getCanTaskReopen($this->Task->getTaskGroupTypeById($this->task_group_id));
	}

	// return a task type object given a task type
	function getTaskTypeObject() {
		if ($this->task_type) {
			return $this->Task->getTaskTypeObject($this->task_type);
		}
	}
	
	// return a task group object given a task group ID
	function getTaskGroupTypeObject() {
		if ($this->task_group_id) { 
			$tgtn = $this->Task->getTaskGroupTypeById($this->task_group_id);
			return $this->Task->getTaskGroupTypeObject($tgtn);
		}
	}
	
	function printSearchTitle() {
		$buf = $this->title.', '.strtoupper($this->status);
		return $buf;
	}
	
	function printSearchListing() {
		$tg = $this->Task->getTaskGroup($this->task_group_id);
		$assignee = $this->getAssignee();
		$buf = $tg->title;
		if ($assignee) {
			$buf .= ', Assigned: '.$assignee->getFullName();
		}
		
		if ($this->dt_first_assigned) {
			$buf .= ', First Assigned: '.$this->getDate('dt_first_assigned');
		}
		
		if ($this->dt_due) {
			$buf .= ', Due: '.$this->getDate('dt_due');
		}
		return $buf;
	}
	
	function printSearchUrl() {
		return "task/viewtask/".$this->id;
	}
	
	function getAssignee() {
		if ($this->assignee_id) {
			return $this->getObject("User", $this->assignee_id);
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::insert()
	 */
	function insert($force_validation = false) {
		if ($this->task_group_id) {
			// set default status for newly created tasks
			$this->status = $this->getTaskGroupTypeObject()->get_default_status($this);

			// if no assignee selected for newly created task, use task group default assignee
			if ($this->first_assignee_id == "") {
				$tg = $this->Task->getTaskGroup($this->task_group_id);
				$this->first_assignee_id = $this->assignee_id = $tg->default_assignee_id;
			}
			else {
				$this->assignee_id = $this->first_assignee_id;
				
			}
		}
		
		// run any pre-insert routines
		if ($this->task_type) {
			$this->getTaskTypeObject()->on_before_insert($this);			
		}
		
		// insert task into database
		parent::insert();
		
		// run any post-insert routines
		
		// add a comment upon task creation
		$comm = new TaskComment($this->w);
	    $comm->obj_table = $this->getDbTableName();
		$comm->obj_id = $this->id;
		$comm->comment = "Task Created";
    	$comm->insert();
		
		// add to context for notifications post listener
    	$this->w->ctx("TaskComment",$comm);
    	$this->w->ctx("TaskEvent","task_creation");
    	
    	if ($this->task_type) {
			$this->getTaskTypeObject()->on_after_insert($this);			
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DbObject::update()
	 */
	function update($force = false, $force_validation = false) {
		if ($this->task_type) {
			$this->getTaskTypeObject()->on_before_update($this);			
		}
		
		parent::update($force, $force_validation);
		
		if ($this->task_type) {
			$this->getTaskTypeObject()->on_after_update($this);			
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DbObject::delete()
	 */
	function delete($force = false) {
		if ($this->task_type) {
			$this->getTaskTypeObject()->on_before_delete($this);			
		}
		
		parent::delete($force);
		
		if ($this->task_type) {
			$this->getTaskTypeObject()->on_after_update($this);			
		}
	}
	
	function getTaskGroup() {
		return $this->Task->getTaskGroup($this->task_group_id);
	}
}
