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

    public $title;   // not null
    public $can_assign;  // ALL, GUEST, MEMBER, OWNER
    public $can_view;   // ALL, GUEST, MEMBER, OWNER
    public $can_create; // ALL, GUEST, MEMBER, OWNER
    public $is_active;  // 0 / 1
    public $is_deleted;  // 0 / 1
    public $description;
    public $default_assignee_id; // can be null
    public $task_group_type; // php class name of concrete TaskGroupType implementation
    public $_modifiable;
    public static $_db_table = "task_group";

    // get my member object. compare my role with group role required to view task group
    function getCanIView() {
        if ($this->w->Auth->user()->is_admin == 1) {
            return true;
        }
        
        $me = $this->Task->getMemberGroupById($this->id, $this->w->Auth->user()->id);
        if (empty($me))
            return false;
        return ($this->can_view == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_view);
    }

    // get my member object. compare my role with group role required to create tasks in this group
    function getCanICreate() {
        if ($this->w->Auth->user()->is_admin == 1) {
            return true;
        }
        
        $me = $this->Task->getMemberGroupById($this->id, $this->w->Auth->user()->id);
        if (empty($me))
            return false;
        return ($this->can_create == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_create);
    }

    // get my member object. compare my role with group role required to assign tasks in this group
    function getCanIAssign() {
        if ($this->w->Auth->user()->is_admin == 1) {
            return true;
        }
        
        $me = $this->Task->getMemberGroupById($this->id, $this->w->Auth->user()->id);
        if (empty($me))
            return false;
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
        $assign = $this->w->Auth->getUser($this->default_assignee_id);
        return $assign ? $assign->getFullName() : "";
    }

    function getTasks() {
        return $this->getObjects("Task", array("task_group_id" => $this->id));
    }

    public function getSelectOptionTitle() {
        return $this->title;
    }

    public function getSelectOptionValue() {
        return $this->id;
    }

}
