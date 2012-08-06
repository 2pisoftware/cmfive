<?php
// defines task group members and their role in the group
class TaskGroupMember extends DbObject {
	var $task_group_id;
	var $user_id;
	var $role; 			// OWNER, MEMBER, GUEST
	var $priority;		// number to assign placement in user's list of groups
	var $is_active;

	// actual table name
	function getDbTableName() {
		return "task_group_member";
	}
}
