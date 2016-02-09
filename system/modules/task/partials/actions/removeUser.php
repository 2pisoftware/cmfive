<?php

function removeUser(Web $w, $params = []) {
	$user = $params['user'];
	$redirect = $params['redirect'];
	
	// Get tasks that are assigned to this user
	$tasks = $w->Task->getObjects("Task", ["is_deleted" => 0, "assignee_id" => $user->id]);
	$task_group_membership = $w->Task->getObjects("TaskGroupMember", ["user_id" => $user->id]);
	
	$default_taskgroup_assignee = 0;
	if (!empty($task_group_membership)) {
		foreach($task_group_membership as $membership) {
			$taskgroup = $membership->getTaskGroup();
			if ($taskgroup->default_assignee_id == $user->id) {
				$default_taskgroup_assignee++;
			}
		}
	}

	$w->ctx("user", $user);
	$w->ctx("default_taskgroup_assignee", $default_taskgroup_assignee);
	$w->ctx("tasks", $tasks);
	$w->ctx("task_group_membership", $task_group_membership);
	$w->ctx("redirect", $redirect);
}
