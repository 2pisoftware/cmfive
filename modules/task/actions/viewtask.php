<?php
function viewtask_GET(Web &$w) {
	$p = $w->pathMatch("id");

	// declare delete button
	$btndelete = "";

	// get relevant object for viewing a task given input task ID
	$task = $w->Task->getTask($p['id']);
	$taskdata = $w->Task->getTaskData($p['id']);
	$group = $w->Task->getTaskGroup($task->task_group_id);

	TaskLib::task_navigation($w, "View Task: " . $task->title);

	// if task is deleted, say as much and return to task list
	if ($task->is_deleted != 0) {
		$w->msg("This Task has been deleted","/task/tasklist/");
	}
	// check if i can view the task: my role in group Vs group can_view value
	elseif ($task->getCanIView()) {
		// tab: Task Details

		// if I can assign tasks, provide dropdown of group members else display current assignee.
		// my role in group Vs group can_assign value
		if ($task->getCanIAssign()) {
			$members = ($task) ? $w->Task->getMembersBeAssigned($task->task_group_id) : $w->auth->getUsers();
			sort($members);
			$assign = array("Assigned To","select","assignee_id",$task->assignee_id,$members);
		}
		else {
			$assigned = ($task->assignee_id == "0") ? "Not Assigned" : $w->Task->getUserById($task->assignee_id);
			$assign = array("Assigned To","static","assignee_id",$assigned);
		}

		//		changing type = dymanically loading of relevant form fields ... problem when presenting on single page.
		//		array("Task Type","select","task_type",$task->task_type,$task->getTaskGroupTypes()),

		// check a due date exists
		$dtdue = (($task->dt_due == "0000-00-00 00:00:00") || ($task->dt_due == "")) ? "" : date('d/m/Y',$task->dt_due);

		// Guests can view but not edit
		// See if i am assignee or creator, if yes provide editable form, else provide static display
		if ($task->getCanIEdit()) {
			$btndelete = Html::b($webroot."/task/deletetask/".$task->id," Delete Task ", "Are you should you with to DELETE this task?");

			// if task is closed and Task Group type says cannot be reopened, display static status
			if ($task->getisTaskClosed() && !$task->getTaskReopen()) {
				$status = array("Status","static","status",$task->status);
			}
			// otherwise, task is open, or is closed but can be reopened so allow edit of status
			else {
				$status = array("Status","select","status",$task->status,$task->getTaskGroupStatus());
			}
				
			$f = array(
			array("Task Details","section"),
			array("Title","text", "title", $task->title),
			array("Created By","static", "creator", $task->getTaskCreatorName()),
			array("Task Group","static","tg",$task->getTaskGroupTypeTitle()),
			array("Task Type","static","task_type",$task->getTypeTitle()),
			array("Description","static","tdesc",$task->getTypeDescription()),
			$status,
			array("Priority","select","priority",$task->priority,$task->getTaskGroupPriority()),
			array("Date Due","date","dt_due", $dtdue),
			array("Description","textarea", "description",$task->description,"80","15"),
			$assign,
			);
		}
		else {
			$f = array(
			array("Task Details","section"),
			array("Title","static", "title", $task->title),
			array("Created By","static", "creator", $task->getTaskCreatorName()),
			array("Task Group","static","tg",$task->getTaskGroupTypeTitle()),
			array("Task Type","static","task_type",$task->getTypeTitle()),
			array("Description","static","tdesc",$task->getTypeDescription()),
			array("Status","static","status",$task->status),
			array("Priority","static","priority",$task->priority),
			array("Date Due","static","dt_due", $dtdue),
			array("Description","static", "description",str_replace("\r\n","<br>",$task->description)),
			$assign,
			);
		}

		// got additional form fields for this task type
		$form = $w->Task->getFormFieldsByTask($task->task_type,$group);

		// if there are additional form fields, display them
		if ($form) {
			// string match form fields with task data by key
			// can then push db:value into form field for display
			foreach ($form as $x) {
				if ($x[1] == "section") {
					array_push($f, $x);
				}

				if ($taskdata) {
					foreach ($taskdata as $td) {
						$key = $td->key;
						$value = $td->value;

						// Guests can view but not edit
						// See if i am a guest, if yes provide static display, else provide editable form
						if (!$task->getCanIEdit())
						$x[1] = "static";
							
						if ($key == $x[2]) {
							$x[3] = $value;
							array_push($f, $x);
						}
					}
				}
				else {
					if ($x[1] != "section")
					array_push($f, $x);
				}
			}
		}

		// create form
		$form = Html::form($f,$w->localUrl("/task/updatetask/".$task->id),"POST"," Update ");

		// create 'start time log' button
		if ($task->assignee_id == $w->auth->user()->id) {
			$btntimelog = "<button class=\"startTime\" href=\"/task/starttimelog/".$task->id."\"> Start Time Log </button>";
		}

		// display variables
		$w->ctx("btntimelog",$btntimelog);
		$w->ctx("btndelete",$btndelete);
		$w->ctx("viewtask",$form);
		$w->ctx("extradetails",$task->displayExtraDetails());

		// tab: Task Comments
		// provide button for adding new comments
		$add_c = Html::box($w->localUrl("/task/editComment/".$task->id),"Add a New Comment",true);
		$w->ctx("addComment",$add_c);

		// provide current comment count in the tab heading for Comments
		$numComments = $task->countTaskComments();
		$w->ctx("numComments",$numComments);

		// get the comments list for this task
		$comments = $task->getTaskComments();

		// build the table of comments
		$line = array(array("Comments for Task: " . $task->title));
		if ($comments) {
			foreach($comments as $com) {
				$line[] = array("<dt><b>".formatDateTime($com->dt_created)."</b><dd>".str_replace("\n","<br>",$w->Task->findURL($com->comment))." - ".$w->Task->getUserById($com->modifier_id) . "</dd>");
				// edit comments?
				//Html::box($w->localUrl("/task/editComment/".$task->id."/".$com->id)," Edit ",true)
			}
		}
		else {
			$line[] = array("There are no comments for this task");
		}

		// display the table of comments
		$w->ctx("comments",Html::table($line,null,"tablesorter",true));

		//tab: Task Documents
		// provide a button for adding new documents
		$line = array();
		$putdocos = Html::box($webroot."/task/attachForm/".$task->id," Upload a Document ",true);
		$w->ctx("btnAttachment",$putdocos);

		// provide current document + page count in tab heading for Documents
		$numDocos = $task->countTaskDocos();
		$num = intval($numDocos);
		if ($num == "") {
			$num = "0";
		}

		$w->ctx("numDocos",$num);

		// get the list of documents
		$docos = $task->getTaskDocos();
		// get the list of pages accessible to me
		// build the table of documents
		$hds = array(array("Document", "Uploaded by", "Date", "Description"));

		// if documents, list them
		if ($docos)	{
			foreach ($docos as $doco) {
				$line[] = array("<a href=\"" . $webroot . "/file/atfile/" . $doco->id . "/" . $doco->filename . "\" target=\"_blank\">" . $doco->filename . "</a>",
				$w->Task->getUserById($doco->modifier_user_id),
				formatDateTime($doco->dt_created),
				$doco->description,
				);
			}
		}


		// if no documents or pages, say as much
		if (!$line) {
			$line[] = array("There are no documents attached to this task", "", "", "");
		}

		// put column headings onto doco/page list
		$line = array_merge($hds, $line);

		// display the table of documents
		$w->ctx("docos",Html::table($line,null,"tablesorter",true));

		// tab: time log
		// provide button to add time entry
		if ($task->assignee_id == $w->auth->user()->id) {		
			$addtime = Html::box($webroot."/task/addtime/".$task->id," Add Time Log entry ",true);
		}
		$w->ctx("addtime",$addtime);

		// get time log for task
		$timelog = $task->getTimeLog();

		// set total period
		$totseconds = 0;

		// set headings
		$line = array(array("Assignee", "Created By", "Start", "End", "Period (hours)", ""));
		// if log exists, display ...
		if ($timelog) {
			// for each entry display, calculate period and display total time on task
			foreach ($timelog as $log) {
				// get time difference, start to end
				$seconds = $log->dt_end - $log->dt_start;
				$period = $w->Task->getFormatPeriod($seconds);

				// if suspect, label button, style period, remove edit button
				if ($log->is_suspect == "1") {
					$label = " Accept ";
					$period = "(" . $period . ")";
					$bedit = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				// if accepted, label button, tally period, include edit button
				if ($log->is_suspect == "0") {
					$label = " Review ";
					$totseconds += $seconds;
					$bedit = Html::box($w->localUrl("/task/addtime/".$task->id."/".$log->id)," Edit ",true);
				}

				// ony Task Group owner gets to reject/accept time log entries
				$bsuspect = ($w->Task->getIsOwner($task->task_group_id, $_SESSION['user_id'])) ? Html::b($w->localUrl("/task/suspecttime/".$task->id."/".$log->id),$label) : "";

				$line[] = array($w->Task->getUserById($log->user_id),
				$w->Task->getUserById($log->creator_id),
				formatDateTime($log->dt_start),
				formatDateTime($log->dt_end),
				$period,
				$bedit .
								"&nbsp;" . 
				Html::b($w->localUrl("/task/deletetime/".$task->id."/".$log->id)," Delete ","Are you sure you wish to DELETE this Time Log Entry?") .
								"&nbsp;" . 
				$bsuspect .
								"&nbsp;" . 
				Html::box($w->localUrl("/task/popComment/".$task->id."/".$log->comment_id)," Comment ",true)
				);
			}
			$line[] = array("","","","<b>Total</b>", "<b>".$w->Task->getFormatPeriod($totseconds)."</b>","");
		}
		else {
			$line[] = array("No time log entries have been made","","","","","");
		}

		// display the task time log
		$w->ctx("timelog",Html::table($line,null,"tablesorter",true));

		// tab: notifications
		// if i am assignee, creator or task group owner, i can get notifications for this task
		if ($task->getCanINotify()) {
			// if i can get notifications for this Task, display tbe navigation tab
			$tab = "<a id=\"tab-link-5\" href=\"#\" onclick=\"switchTab(5);\">Task Notifications</a>\n";
			$w->ctx("tasknotifications",$tab);
				
			// get User set notifications for this Task
			$notify = $w->Task->getTaskUserNotify($_SESSION['user_id'],$task->id);
			if ($notify) {
				$task_creation = $notify->task_creation;
				$task_details = $notify->task_details;
				$task_comments = $notify->task_comments;
				$time_log = $notify->time_log;
				$task_documents = $notify->task_documents;
			}
			// no user notifications, get user set notifications for the Task Group
			else {
				// need my role in group
				$me = $w->Task->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
				// get task creator ID
				$creator_id = $task->getTaskCreatorId();

				// which am i?
				$assignee = ($task->assignee_id == $_SESSION['user_id']) ? true : false;
				$creator = ($creator_id == $_SESSION['user_id']) ? true : false;
				$owner = $w->Task->getIsOwner($task->task_group_id, $_SESSION['user_id']);

				// get single type given this is specific to a single Task
				if ($assignee) {
					$type = "assignee";
				}
				elseif ($creator) {
					$type = "creator";
				}
				elseif ($owner) {
					$type = "other";
				}

				$role = strtolower($me->role);

				if ($type) {
					// for type, check the User defined notification table
					$notify = $w->Task->getTaskGroupUserNotifyType($_SESSION['user_id'],$task->task_group_id,$role,$type);

					// get list of notification flags
					if ($notify) {
						if ($notify->value == "1") {
							$task_creation = $notify->task_creation;
							$task_details = $notify->task_details;
							$task_comments = $notify->task_comments;
							$time_log = $notify->time_log;
							$task_documents = $notify->task_documents;
							$task_pages = $notify->task_pages;
						}
					}
				}
			}
				
			// create form. if still no 'notify' all boxes are unchecked
			$f = array(array("For which Task Events should you receive Notification?","section"));
			$f[] = array("","hidden","task_creation", "0");
			$f[] = array("Task Details Update","checkbox","task_details", $task_details);
			$f[] = array("Comments Added","checkbox","task_comments", $task_comments);
			$f[] = array("Time Log Entry","checkbox","time_log", $time_log);
			$f[] = array("Task Data Updated","checkbox","task_data", $task_data);
			$f[] = array("Documents Added","checkbox","task_documents", $task_documents);

			$form = Html::form($f,$w->localUrl("/task/updateusertasknotify/".$task->id),"POST","Save");

			// display form in tab.div
			$tasknotify = "<div id=\"tab-5\" style=\"display: none;\">\n" .
						  "Set your Notifications specific to this Task, otherwise your notifications for this Task Group will be employed.\n" .
			    		  "<p>\n" .
			$form .
						  "</div>\n";

			// display
			$w->ctx("tasknotify",$tasknotify);
		}
	}
	else {
		// if i cannot view task details, return to task list with error message
		// for display get my role in the group, the group owners, the group title and the minimum membership required to view a task
		$me = $w->Task->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
		$myrole = (!$me) ? "Not a Member" : $me->role;
		$owners = $w->Task->getTaskGroupOwners($task->task_group_id);

		// get owners names for display
		foreach ($owners as $owner) {
			$strOwners .= $w->Task->getUserById($owner->user_id) . ", ";
		}
		$strOwners = rtrim($strOwners,", ");

		$form = "You must be at least a <b>" . $group->can_view . "</b> of the Task Group: <b>" . strtoupper($group->title) . "</b>, to view tasks in this group.<p>Your current Membership Level: <b>" . $myrole . "</b><p>Please appeal to the group owner(s): <b>" . $strOwners . "</b> for promotion.";

		$w->error($form,"/task/tasklist");
	}

}
