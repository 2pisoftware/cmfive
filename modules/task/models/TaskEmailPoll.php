<?php
/**
 * 
 * Enable creation of new tasks via email
 * 
 * @author carsten
 *
 */
class TaskEmailPoll extends DbObject {
	var $task_group_id;
	var $email_address;
	var $pop_login;
	var $pop_password;
	var $pop_host;
	var $pop_port;
	var $pop_method; 			// SSL, PLAIN, TFL
	var $default_task_type;
	var $default_task_priority;
	var $default_task_status;
	var $default_assignee_id;
	var $accept_non_users;		// if set, then anyone can post to this group, even non Flow users
	var $default_non_user_id;	// select a user who appears as sender, when accepting posts from non-users
	var $reply_non_user_on_new_task;		// 0 / 1 whether to send a task creation email to non-flow users!
}
