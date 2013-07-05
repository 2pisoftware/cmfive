<?php
// edit a member
function editmember_GET(Web &$w) {
	$p = $w->pathMatch("repid","userid");
	// get member details for edit
	$member = $w->Report->getReportMember($p['repid'], $p['userid']);

	// build editable form for a member allowing change of membership type
	$f = Html::form(array(
	array("Member Details","section"),
	array("","hidden", "report_id",$p['repid']),
	array("Name","static", "name", $w->Report->getUserById($member->user_id)),
	array("Role","select","role",$member->role,$w->Report->getReportPermissions())
	),$w->localUrl("/report/editmember/".$p['userid']),"POST"," Update ");

	// display form
	$w->setLayout(null);
	$w->ctx("editmember",$f);
}

function editmember_POST(Web &$w) {
	$p = $w->pathMatch("id");
	$member = $w->Report->getReportMember($_POST['report_id'], $p['id']);

	$member->fill($_REQUEST);
	$member->update();

	$w->msg("Member updated","/report/viewreport/".$_POST['report_id']."?tab=2");
}
