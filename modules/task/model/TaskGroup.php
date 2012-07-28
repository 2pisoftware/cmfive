<?php
/**
 * 
 * A Task group defines the type of tasks which can be
 * assigned to this group, as well as the people who
 * participate in this group.
 * 
 * @author carsten
 *
 */
class TaskGroup extends DbObject {
	var $title;			// not null
	var $can_assign; 	// ALL, GUEST, MEMBER, OWNER
	var $can_view; 		// ALL, GUEST, MEMBER, OWNER
	var $can_create;	// ALL, GUEST, MEMBER, OWNER
	var $is_active; 	// 0 / 1
	var $is_deleted; 	// 0 / 1
	var $description;
	var $default_assignee_id; // can be null
	var $task_group_type; // php class name of concrete TaskGroupType implementation
	
	var $_modifiable;
	
	// actual table name
	function getDbTableName() {
		return "task_group";
	}
	
	// get my member object. compare my role with group role required to view task group
	function getCanIView() {
		$me = $this->Task->getMemberGroupById($this->id, $_SESSION['user_id']);
		return ($this->can_view == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_view); 
	}
	
	// get my member object. compare my role with group role required to create tasks in this group
	function getCanICreate() {
		$me = $this->Task->getMemberGroupById($this->id, $_SESSION['user_id']);
		return ($this->can_create == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_create); 
	}
	
	// get my member object. compare my role with group role required to assign tasks in this group
	function getCanIAssign() {
		$me = $this->Task->getMemberGroupById($this->id, $_SESSION['user_id']);
		return ($this->can_assign == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_assign); 
	}
	
	// get task group title given task group type
	function getTypeTitle() {
		$c = $this->Task->getTaskGroupTypeObject($this->task_group_type);
		return $c ? $c->getTaskGroupTypeTitle() : "unknown";
	}
	
	// get task group description given task group type
	function getTypeDescription() {
		$c = $this->Task->getTaskGroupTypeObject($this->task_group_type);
		return $c ? $c->getTaskGroupTypeDescription() : "unknown";
	}
	
	// get fullname of default assignee for this task group
	function getDefaultAssigneeName() {
		$assign = $this->w->auth->getUser($this->default_assignee_id);
		return $assign ? $assign->getFullName() : "";
	}
}
