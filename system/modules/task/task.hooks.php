<?php

// Different task notification events as defined in the database
define('TASK_NOTIFICATION_TASK_CREATION', 'task_creation');
define('TASK_NOTIFICATION_TASK_DETAILS', 'task_details');
define('TASK_NOTIFICATION_TASK_COMMENTS', 'task_comments');
define('TASK_NOTIFICATION_TIME_LOG', 'time_log');
define('TASK_NOTIFICATION_TASK_DOCUMENTS', 'task_documents');

/**
 * Add custom time type object to timelogs
 * 
 * @param Web $w
 * @param Task $object
 */
function task_timelog_type_options_for_Task(Web $w, $object) {
	if (!empty($object)) {
		$task_type = $w->Task->getTaskTypeObject($object->task_type);
		$time_types = $task_type->getTimeTypes();
		if (!empty($time_types)) {
			return [["Task time", "select", "time_type", $object->time_type, $time_types]];
		}
	}
}

/**
 * Hook to notify relevant people when a task has been created
 * 
 * @param Web $w
 * @param Task $object
 */
function task_core_dbobject_after_insert_Task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_insert_Task");
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($object, TASK_NOTIFICATION_TASK_CREATION);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_CREATION);
        
        // send it to the inbox of the user's on our send list
		// prepare our message, add heading, add URL to task, add notification advice in messgae footer 
		$subject = $event_title . "[" . $object->id . "]: " . $object->title;
        $logged_in_user = $w->Auth->user();
		
        foreach ($users_to_notify as $user) {
            $message = "<b>" . $event_title . " [" . $object->id . "]</b><br/>\n";
            $message .= "<p>A new task has been created</p>";
            
			$message .= "<p><b>Subject:</b> " . $object->title . "</p>";
			$message .= "<p><b>Body:</b>" . $object->description . "</p>";
			
            $user_object = $w->Auth->getUser($user);
            $message .= "<br/><p>Access the task here: " . $object->toLink(null, null, $user_object) . "</p>";
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

			$attachments = $w->File->getAttachmentsFileList($object);
			
			if (!$logged_in_user || $logged_in_user->id !== $user_object->id) {
				$w->Mail->sendMail(
					$user_object->getContact()->email, 
					!empty($logged_in_user->id) ? $logged_in_user->getContact()->email : Config::get('main.company_support_email'),
					$subject, $message, null, null, $attachments
				);
			}
			
			// Add message to inbox (needed?) but dont send an email
            $w->Inbox->addMessage($subject, $message, $user, null, null, false);
        }
    }
}

/**
 * Hook to notify relevant people when a task has been update
 * 
 * @param Web $w
 * @param Task $object
 */
function task_core_dbobject_after_update_Task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_update_Task");
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($object, TASK_NOTIFICATION_TASK_DETAILS);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DETAILS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = "Task - " . $object->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $object->title . " details has been updated</p>";
            
            $user_object = $w->Auth->getUser($user);
            $message .= $object->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            $w->Inbox->addMessage($subject, $message, $user);
        }
    }
}

/**
 * Hook to notify relevant people when a task has been updated
 * 
 * @param Web $w
 * @param Task $object
 */
function task_comment_comment_added_task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_comment_comment_added_task");
    
    $task = $w->Task->getTask($object->obj_id);
    
    if (empty($task->id)) {
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_COMMENTS);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    $comment_user = $w->Auth->getUser($object->creator_id);
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_COMMENTS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = (!empty($comment_user->id) ? $comment_user->getFullName() : 'Someone') . ' has commented on a task that you\'re apart of ('.$task->title.')';

            $user_object = $w->Auth->getUser($user);
            $message = $task->toLink(null, null, $user_object);
            $message .= $w->partial("displaycomment", array("object" => $object, "displayOnly" => true, 'redirect' => '/inbox'), "admin");
            
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            $w->Inbox->addMessage($subject, $message, $user, null, null, true);
        }
    }
}

/**
 * Hook to notify relevant people when a task has been updated
 * 
 * @param Web $w
 * @param Task $object
 */
function task_comment_comment_added_comment(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_comment_comment_added_comment");
    
    // Check if the parent comment is attached to a task
    $comment = $object;
    while(strtolower($comment->obj_table) == "comment" && $comment->obj_id != NULL) {
        $comment = $w->Comment->getComment($comment->obj_id);
        
        // Check if the comment could not be found
        if (empty($comment->id)) {
            $w->Log->setLogger("TASK")->debug("Comment not found");
            return;
        }
    }
    
    // We only want task comments!
    if (strtolower($comment->obj_table) != "task") {
        $w->Log->setLogger("TASK")->debug("Comment parent not a task");
        return;
    }
    
    $task = $w->Task->getTask($comment->obj_id);
    if (empty($task->id)) {
        $w->Log->setLogger("TASK")->debug("Task not found");
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_COMMENTS);
    if (!in_array($task->assignee_id, $users_to_notify)) {
        $users_to_notify[$task->assignee_id] = $task->assignee_id;
    }
    
    // Add all users in comment thread to the notification
    $reply_comment = $object;
    $comment_thread_users = array();
    while(strtolower($reply_comment->obj_table) == "comment" && $comment->obj_id != NULL) {
        if (!in_array($reply_comment->creator_id, $users_to_notify)) {
            $comment_thread_users[$reply_comment->creator_id] = $reply_comment->creator_id;
        }
        $reply_comment = $w->Comment->getComment($comment->obj_id);
        
        // Check if the comment could not be found
        if (empty($comment->id)) {
            return;
        }
    }
    $users_to_notify = array_merge($comment_thread_users, $users_to_notify);
    $comment_user = $w->Auth->getUser($object->creator_id);
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_COMMENTS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = $comment_user->getFullName() . " replied to a comment " . (in_array($w->Auth->user()->id, $comment_thread_users) ? "that you're a part of " : "") . "for ". $task->title;
            $message = "<p>Comment</p>";
            $message .= $w->partial("displaycomment", array("object" => $object, "displayOnly" => true, 'redirect' => '/inbox'), "admin");
            
            $user_object = $w->Auth->getUser($user);
            if ($task->canView($user_object)) {
                $message .= "<a href='/task/edit/" . $task->id . "?scroll_comment_id=" . $object->id . "#comments'><p>Click here to view the comment</p></a>";            
            } else {
                $message .= "<p><b>You are unable to view this task</b></p>";
            }
            
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            $w->Inbox->addMessage($subject, $message, $user, null, null, true);
        }
    }
}

function task_core_dbobject_after_insert_TaskTime(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_core_dbobject_after_insert_TaskTime");
    
    $task = $object->getTask();
    
    if (empty($task->id)) {
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TIME_LOG);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TIME_LOG);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = "Task - " . $task->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $task->title . " as had a new time log entry</p>";
            
            $user_object = $w->Auth->getUser($user);
            $message .= $task->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            $w->Inbox->addMessage($subject, $message, $user);
        }
    }
}

function task_attachment_attachment_added_task(Web $w, $object) {
    $w->Log->setLogger("TASK")->debug("task_attachment_attachment_added_task");
    
    $task = $w->Task->getTask($object->parent_id);
    
    if (empty($task->id)) {
        return;
    }
    
    $users_to_notify = $w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_DOCUMENTS);
    $w->Log->setLogger("TASK")->info("Notifying " . count($users_to_notify) . " users");
    
    if (!empty($users_to_notify)) {
        $event_title = $object->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_DOCUMENTS);
        
        // send it to the inbox of the user's on our send list
        foreach ($users_to_notify as $user) {
            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
            $subject = "Task - " . $task->title . ": " . $event_title;
            $message = "<b>" . $event_title . "</b><br/>\n";
            $message .= "<p>" . $task->title . " as got a new attachment</p>";
            
            $user_object = $w->Auth->getUser($user);
            $message .= $task->toLink(null, null, $user_object);
            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist#notifications", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

            $w->Inbox->addMessage($subject, $message, $user);
        }
    }
}

//function task_core_web_after_post_task(Web $w) {
//    $w->Log->setLogger("TASK")->debug("task_core_web_after_post_task");
//    
//    // listening to Task module for adding comments
//    if ($w->ctx("TaskComment") != null && $w->ctx("TaskEvent") != null) {
//        // the task event type
//        $event = $w->ctx("TaskEvent");
//        // the task comment
//        $comm = $w->ctx("TaskComment");
//
//        // the task
//        $task = $w->Task->getTask($comm->obj_id);
//
//        // people requiring notifications include: creator, assignee, owner
//        // when returning creator, assignee we have a single object, where returning owners we have an array of objects
//        // therefore, for merge, need to wrap creator and assignee objects in array.
//        // get my member object for this task group
//        $me = $w->Task->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
//        $me = array($me);
//        // get member object for task creator
//        $creator_id = $task->getTaskCreatorId();
//        $creator = $w->Task->getMemberGroupById($task->task_group_id, $creator_id);
//        $creator = array($creator);
//        // get member object(s) for task group owner(s)
//        $owners = $w->Task->getTaskGroupOwners($task->task_group_id);
//
//        // us is everyone
//        $us = (object) array_merge((array) $me, (array) $creator, (array) $owners);
//
//        // foreach relavent member
//        foreach ($us as $i) {
//            // set default notification value. 0 = no notification
//            $value = "0";
//            // set current user's role
//            $role = strtolower($i->role);
//            // determine current user's 'type' for this task
//            $assignee = ($task->assignee_id == $i->user_id) ? true : false;
//            $creator = ($creator_id == $i->user_id) ? true : false;
//            $owner = $w->Task->getIsOwner($task->task_group_id, $i->user_id);
//
//            // this user may be any or all of the 'types'
//            // need to check each 'type' for a notification
//            if (!empty($assignee)) {
//                $types[] = "assignee";
//            }
//            if (!empty($creator)) {
//                $types[] = "creator";
//            }
//            if (!empty($owner)) {
//                $types[] = "other";
//            }
//
//            // if they have a type ... look for notifications
//            if (!empty($types)) {
//                // check user task notifications
//                $notify = $w->Task->getTaskUserNotify($i->user_id, $task->id);
//
//                // if there is a record, get notification flag
//                if (!empty($notify)) {
//                    $value = $notify->$event;
//                }
//                // if no user task notification present, check user task group notification for role and type
//                else {
//                    // for each type, check the User defined notification table 
//                    foreach ($types as $type) {
//                        $notify = $w->Task->getTaskGroupUserNotifyType($i->user_id, $task->task_group_id, $role, $type);
//
//                        // if there is a notification flag and it equals 1, no need to go further, a notification will be sent
//                        if (!empty($notify)) {
//                            if ($notify->value == "1") {
//                                $value = $notify->$event;
//                                break;
//                            }
//                        }
//                    }
//                }
//
//                // if no user task group notification present, check task group default notification for role and type
//                if (empty($notify)) {
//                    foreach ($types as $type) {
//                        $notify = $w->Task->getTaskGroupNotifyType($task->task_group_id, $role, $type);
//
//                        // if notification exists, set its value
//                        if (!empty($notify)) {
//                            $value = $notify->value;
//                        }
//
//                        // if its value is 1, no need to go further, a notification will be sent
//                        if ($value == "1") {
//                            break;
//                        }
//                    }
//                }
//
//                // if somewhere we have found a positive notification, add user_id to our send list
//                if ($value == "1") {
//                    $notifyusers[$i->user_id] = $i->user_id;
//                }
//            }
//            unset($types);
//        }
//
//        // if we have a list of user_id's to send to ...
//
//        if (!empty($notifyusers)) {
//            // use the task event as a title. want some formatting
//            $cap = array();
//            $arr = preg_split("/_/", $event);
//            foreach ($arr as $a) {
//                $cap[] = ucfirst($a);
//            }
//            $eventtitle = implode(" ", $cap);
//
//            // prepare our message, add heading, add URL to task, add notification advice in messgae footer 
//            $subject = "Task - " . $task->title . ": " . $eventtitle;
//            $message = "<br/>\n";
//            $message .= "<b>" . $eventtitle . "</b><br/>\n";
//            $message .= $comm->comment . "<p>";
//            $message .= Html::a(WEBROOT . "/task/viewtask/" . $comm->obj_id, "View Task");
//            $message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT . "/task/tasklist/?tab=2", "Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";
//
//            // send it to the inbox of the user's on our send list
//
//            foreach ($notifyusers as $user) {
//                $w->Inbox->addMessage($subject, $message, $user);
//            }
//        }
//    }
//}
//
