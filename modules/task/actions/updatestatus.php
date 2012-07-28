<?php
// update status using dropdowns provided on Task List
function updatestatus_ALL(Web &$w) {
	// check for required REQUEST elements
	if (($_REQUEST['id'] != "") && ($_REQUEST['status'] != "")) {
		// task is to get updated so gather relevant data
		$task = $w->Task->getTask($_REQUEST['id']);

		// if task exists, first gather changes for display in comments
		if ($task) {
			$comments = "status updated to: " . $_REQUEST['status'] . "\n";

			$task->fill($_REQUEST);

			// if task has a 'closed' status, set flag so task no longer appear in dashboard count or task list
			if ($task->getisTaskClosed()) {
				$task->is_closed = 1;
				$task->dt_completed = date("d/m/Y");
			}
			else {
				$task->is_closed = 0;
			}
				
			$task->update();

			// we have comments, so add them
			$comm = new TaskComment($w);
			$comm->obj_table = $task->getDbTableName();
			$comm->obj_id = $task->id;
			$comm->comment = $comments;
			$comm->dt_created = Date("c");
			$comm->is_deleted = 0;
			$comm->insert();

			// add to context for notifications post listener
			$w->ctx("TaskComment",$comm);
			$w->ctx("TaskEvent","task_details");
		}
		// return
		$w->msg("Task: " . $task->title . " updated.","/task/tasklist/?assignee=".$_REQUEST['assignee']."&creator=".$_REQUEST['creator']."&taskgroups=".$_REQUEST['taskgroups']."&tasktypes=".$_REQUEST['tasktypes']."&tpriority=".$_REQUEST['tpriority']."&status=".$_REQUEST['tstatus']."&dt_from=".$_REQUEST['dt_from']."&dt_to=".$_REQUEST['dt_to']);
	}

	// return
	$w->msg("There was a problem.","/task/tasklist/");

}
