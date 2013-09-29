<?php
function tasklist_ALL(Web $w) {
	TaskLib::task_navigation($w, "");

	// tab: tasks
	// prepare default filter dropdowns
	// get WHO to return relevant tasks:
	//		a selected assignee, a blank assignee = all assignee's, no assignee = tasks assigned to me
	$who = (!array_key_exists("assignee",$_REQUEST)) ? $_SESSION['user_id'] : $w->request('assignee');

	// for those groups of which i am a member, get list of all members for display in Assignee & Creator dropdowns
	$mygroups = $w->Task->getMemberGroups($_SESSION['user_id']);

	if ($mygroups){
		foreach ($mygroups as $mygroup) {
			$mymembers = $w->Task->getMembersInGroup($mygroup->task_group_id);
			foreach ($mymembers as $mymem) {
				$members[$mymem[1]] = array($mymem[0],$mymem[1]);
			}
		}
		sort($members);
	}
	// load the search filters
	$a = Html::select("assignee",$members, $who);
	$w->ctx("assignee",$a);

	$b = Html::select("creator",$members, $w->request('creator'));
	$w->ctx("creator",$b);

	$taskgroups = array();
	$c = Html::select("taskgroups",$taskgroups);
	$w->ctx("taskgroups",$c);

	$tasktypes = array();
	$d = Html::select("tasktypes",$tasktypes);
	$w->ctx("tasktypes",$d);

	$tpriority = array();
	$e = Html::select("tpriority",$tpriority);
	$w->ctx("tpriority",$e);

	$status = array();
	$f = Html::select("status",$status);
	$w->ctx("status",$f);

	$g = Html::checkbox("closed", $w->request('closed'));
	$w->ctx("closed",$g);

	// change filter dropdowns to show selectedIndex for current search
	$w->ctx("reqTaskgroups",$w->request('taskgroups'));
	$w->ctx("reqTasktypes",$w->request('tasktypes'));
	$w->ctx("reqPriority",$w->request('tpriority'));
	$w->ctx("reqStatus",$w->request('status'));
	$w->ctx("reqdtFrom",$w->request('dt_from'));
	$w->ctx("reqdtTo",$w->request('dt_to'));

	// prepare WHERE clause as string
	$where = "";
	if ($w->request('taskgroups') != "")
	$where .= "t.task_group_id = '" . $w->request('taskgroups') . "' and ";
	if ($w->request('tasktypes') != "")
	$where .= "t.task_type = '" . $w->request('tasktypes') . "' and ";
	if ($w->request('tpriority') != "")
	$where .= "t.priority = '" . $w->request('tpriority') . "' and ";
	if (($w->request('status') != ""))
	$where .= "t.status = '" . $w->request('status') . "' and ";
	if (($w->request('status') == "") && ($w->request('closed')))
	$where .= "(t.is_closed = 0 or t.is_closed = 1) and ";
	if ((array_key_exists("status",$_REQUEST)) && ($_REQUEST['status'] == "") && (!$_REQUEST['closed']))
	$where .= "t.is_closed = 0 and ";
	if ($w->request('dt_from') != "")
	$where .= "t.dt_due >= '" . $w->Task->date2db($w->request('dt_from')) . "' and ";
	if ($w->request('dt_to') != "")
	$where .= "t.dt_due <= '" . $w->Task->date2db($w->request('dt_to')) . "' and ";

	$where = rtrim($where, " and ");

	// create task list heading
	$hds = array(array("Title","Assigned To", "Group", "Type", "Priority", "Created By", "Status", "Due", "Time Log"));

	// either use sql join to object_modified, if searching for tasks 'created by' or getObjects for all other searches
	if ($w->request('creator') != "") {
		$tasks = $w->Task->getCreatorTasks($w->request('creator'),$where);
	}
	else {
		$tasks = $w->Task->getTasks($who,$where);
	}

	// show all tasks found
	if ($tasks) {
		usort($tasks, array("TaskService", "sortTasksbyDue"));
		foreach ($tasks as $task) {
			// if i can edit the task, allow me to edit the status from the Task List
			if ($task->getCanIEdit()) {
				if ($task->getisTaskClosed() && !$task->getTaskReopen()) {
					$taskstatus = $task->status;
				}
				else {
					$taskstatus = Html::select("status_".$task->id,$task->getTaskGroupStatus(), $task->status);
				}
			}
			else {
				$taskstatus = $task->status;
			}
				
			$thisline = array(
			Html::a(WEBROOT."/task/viewtask/".$task->id,$task->title),
			$w->Task->getUserById($task->assignee_id),
			$task->getTaskGroupTypeTitle(),
			$task->getTypeTitle(),
			$task->priority,
			$task->getTaskCreatorName(),
			$taskstatus,
			$task->isTaskLate(),
			$task->assignee_id == $w->Auth->user()->id ? 
			Html::a(WEBROOT."/task/starttimelog/".$task->id,"Start Log","Start Log","startTime") :
			"",
			);
			$line[] = $thisline;
		}
	}

	// if no tasks found, say as much
	if (!$line)
	$line = array(array("No Tasks found.","","","","","","","",""));

	$line = array_merge($hds, $line);

	// if logged in user is owner of current group, display button to edit the task group
	$btnedit = Html::b("/task-group/viewmembergroup/".$w->request('taskgroups')," Edit Task Group ");
	$grpedit = ($w->request('taskgroups') != "") && ($w->Task->getIsOwner($w->request('taskgroups'), $_SESSION['user_id'])) ? $btnedit : "";
	$w->ctx("grpedit",$grpedit);

	// display task list
	$w->ctx("mytasks",Html::table($line,null,"tablesorter",true));

	// tab: notifications
	// list groups and notification based on my role and permissions
	$line = array(array("Task Group","Your Role","Creator","Assignee","All Others",""));

	if ($mygroups){
		usort($mygroups, array("TaskService", "sortbyRole"));

		foreach ($mygroups as $mygroup) {
			$taskgroup = $w->Task->getTaskGroup($mygroup->task_group_id);
			$caniview = $taskgroup->getCanIView();
				
			$notify = $w->Task->getTaskGroupUserNotify($_SESSION['user_id'],$mygroup->task_group_id);
			if ($notify) {
				foreach ($notify as $n) {
					$value = ($n->value == "0") ? "No" : "Yes";
					$v[$n->role][$n->type] = $value;
				}
			}
			else {
				$notify = $w->Task->getTaskGroupNotify($mygroup->task_group_id);
				if ($notify) {
					foreach ($notify as $n) {
						$value = ($n->value == "0") ? "No" : "Yes";
						$v[$n->role][$n->type] = $value;
					}
				}
			}

			if ($caniview) {
				$title = $w->Task->getTaskGroupTitleById($mygroup->task_group_id);
				$role = strtolower($mygroup->role);

				$line[] = array(
				$title,
				ucfirst($role),
				$v[$role]["creator"],
				$v[$role]["assignee"],
				$v[$role]["other"],
				Html::box(WEBROOT."/task/updateusergroupnotify/".$mygroup->task_group_id," Edit ",true)
				);
			}
			unset($v);
		}

		// display list
		$w->ctx("notify",Html::table($line,null,"tablesorter",true));
	}
}
