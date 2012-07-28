<?php
// add members to a report
function updatemembers_POST(Web &$w) {
	$arrdb = array();
	$arrdb['report_id'] = $_REQUEST['report_id'];
	$arrdb['role'] = $_REQUEST['role'];
	$arrdb['is_deleted'] = 0;

	// for each selected member, complete population of input array
	foreach ($_REQUEST['member'] as $member) {
		$arrdb['user_id'] = $member;
		// find member against report ID
		$mem = $w->Report->getReportMember($arrdb['report_id'], $arrdb['user_id']);

		// if no membership, create it, otherwise update and continue
		if (!$mem) {
			$mem = new ReportMember($w);
			$mem->fill($arrdb);
			$mem->insert();
		}
		else {
			$mem->fill($arrdb);
			$mem->update();
		}

		// prepare input array for next selected member to insert
		unset($arrdb['user_id']);
	}
	// return
	$w->msg("Member Group updated","/report/viewreport/".$arrdb['report_id']."?tab=2");
}
