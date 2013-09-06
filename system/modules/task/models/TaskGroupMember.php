<?php
// defines task group members and their role in the group
class TaskGroupMember extends DbObject {
	public $task_group_id;
	public $user_id;
	public $role; 			// OWNER, MEMBER, GUEST
	public $priority;		// number to assign placement in user's list of groups
	public $is_active;

	// actual table name
	function getDbTableName() {
		return "task_group_member";
	}
}
