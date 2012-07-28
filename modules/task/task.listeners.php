<?php

function task_listener_POST_ACTION(&$w) {
	// listening to Task module for adding comments
	if ($w->currentHandler() == "task" && $w->ctx("TaskComment") && $w->ctx("TaskEvent")) {
		// the task event type
		$event = $w->ctx("TaskEvent");
		// the task comment
		$comm = $w->ctx("TaskComment");
		// the task
		$task = $w->Task->getTask($comm->obj_id);
		
		// people requiring notifications include: creator, assignee, owner
		// when returning creator, assignee we have a single object, where returning owners we have an array of objects
		// therefore, for merge, need to wrap creator and assignee objects in array.
		
		// get my member object for this task group
		$me = $w->Task->getMemberGroupById($task->task_group_id,$_SESSION['user_id']);
		$me = array($me);
		// get member object for task creator
		$creator_id = $task->getTaskCreatorId();
		$creator = $w->Task->getMemberGroupById($task->task_group_id,$creator_id);
		$creator = array($creator);
		// get member object(s) for task group owner(s)
		$owners = $w->Task->getTaskGroupOwners($task->task_group_id);
		
		// us is everyone
		$us = (object) array_merge((array) $me, (array) $creator, (array) $owners);

		// foreach relavent member
		foreach ($us as $i) {
			// set default notification value. 0 = no notification
			$value = "0";
			// set current user's role
			$role = strtolower($i->role);
			// determine current user's 'type' for this task
			$assignee = ($task->assignee_id == $i->user_id) ? true : false;
			$creator = ($creator_id == $i->user_id) ? true : false;
			$owner = $w->Task->getIsOwner($task->task_group_id, $i->user_id);

			// this user may be any or all of the 'types'
			// need to check each 'type' for a notification
			if ($assignee)
				$types[] = "assignee";
			if ($creator)
				$types[] = "creator";
			if ($owner)
				$types[] = "other";

			// if they have a type ... look for notifications
			if ($types) {
				// check user task notifications
				$notify = $w->Task->getTaskUserNotify($i->user_id,$task->id);

				// if there is a record, get notification flag
				if ($notify) {
					$value = $notify->$event;
				}

				// if no user task notification present, check user task group notification for role and type
				if (!$notify) {
					// for each type, check the User defined notification table 
					foreach ($types as $type) {
						$notify = $w->Task->getTaskGroupUserNotifyType($i->user_id,$task->task_group_id,$role,$type);

						// if there is a notification flag and it equals 1, no need to go further, a notification will be sent
						if ($notify) {
							if ($notify->value == "1") {
								$value = $notify->$event;
								break;
							}
						}
					}
				}
					
				// if no user task group notification present, check task group default notification for role and type
				if (!$notify) {
					foreach ($types as $type) {
						$notify = $w->Task->getTaskGroupNotifyType($task->task_group_id,$role,$type);
					
						// if notification exists, set its value
						if ($notify)
							$value = $notify->value;
						
						// if its value is 1, no need to go further, a notification will be sent
						if ($value == "1")
							break;
					}
				}

				// if somewhere we have found a positive notification, add user_id to our send list
				if ($value == "1")
					$notifyusers[$i->user_id] = $i->user_id;
			}
			unset($types);
		}

		// if we have a list of user_id's to send to ...
		if ($notifyusers) {
			// use the task event as a title. want some formatting
			$cap = array();
			$arr = preg_split("/_/",$event);
			foreach ($arr as $a) {
				$cap[] = ucfirst($a); 
			}
			$eventtitle = implode(" ",$cap);
			
			// prepare our message, add heading, add URL to task, add notification advice in messgae footer 
			$subject = "Task - " . $task->title . ": " . $eventtitle;
    	    $message ="<br/>\n";
        	$message .= "<b>" . $eventtitle . "</b><br/>\n";
	        $message .= $comm->comment . "<p>";
			$message .= Html::a(WEBROOT."/task/viewtask/".$comm->obj_id, "View Task");
			$message .= "<br/><br/><b>Note</b>: Go to " . Html::a(WEBROOT."/task/tasklist/?tab=2","Task > Task List > Notifications") . ", to edit the types of notifications you will receive.";

			// send it to the inbox of the user's on our send list
			foreach ($notifyusers as $user) {
	        	$w->Inbox->addMessage($subject,$message, $user);
			}
		}
    }
}

?>
