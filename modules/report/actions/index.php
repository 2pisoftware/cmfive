<?php
//////////////////////////////////////////////////
//			REPORT DASHBOARD					//
//////////////////////////////////////////////////

function index_ALL(Web &$w) {
	ReportLib::navigation($w, "Reports");

	// report approval flag: display appropriate image
	$app[0] = "<img alt=\"No\" src=\"/img/report/no.gif\" style=\"display: block; margin-left: auto; margin-right: auto;\">";
	$app[1] = "<img alt=\"Yes\" src=\"/img/report/yes.gif\" style=\"display: block; margin-left: auto; margin-right: auto;\">";

	// organise criteria
	$who = $w->session('user_id');
	$where = ($_REQUEST['module'] != "") ? " and r.module = '" . $_REQUEST['module'] . "'" : "";
	$where .= ($_REQUEST['category'] != "") ? " and r.category = '" . $_REQUEST['category'] . "'" : "";
	$where .= ($_REQUEST['type'] != "") ? " and r.sqltype like '%" . $_REQUEST['type'] . "%'" : "";

	// get report categories from available report list
	$reports = $w->Report->getReportsbyUserWhere($who, $where);

	// set headings based on role: 'user' sees only approved reports and no approval status
	$line = ($w->Auth->user()->hasRole("report_editor")  || $w->Auth->user()->hasRole("report_admin")) ?
	array(array("Title", "Approved", "Module", "Category",  "Description", "")) :
	array(array("Title", "Module", "Category", "Description",""));

	// if i am a member of a list of reports, lets display them
	if ($reports) {
		foreach ($reports as $rep) {
			$member = $w->Report->getReportMember($rep->id,$who);
				
			// editor & admin get EDIT button
			//			if (($w->Auth->user()->hasRole("report_editor")) || ($w->Auth->user()->hasRole("report_admin"))) {
			if (($member->role == "EDITOR") || ($w->Auth->user()->hasRole("report_admin"))) {
				$btnedit = Html::b($webroot."/report/viewreport/".$rep->id," Edit ");
			}
			else {
				$btnedit = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
				
			// admin also gets DELETE button
			if ($w->Auth->user()->hasRole("report_admin")) {
				$btndelete = Html::b($webroot."/report/deletereport/".$rep->id," Delete ", "Are you sure you want to delete this Report?");
			}
			else {
				$btndelete = "";
			}
				
			// if 'report user' only list approved reports with no approval status flag
			if (($w->Auth->user()->hasRole("report_user")) && (!$w->Auth->user()->hasRole("report_editor")) && (!$w->Auth->user()->hasRole("report_admin"))) {
				if ($rep->is_approved == "1") {
					$line[] = array(
					$rep->title,
					ucfirst($rep->module),
					$rep->getCategoryTitle(),
					$rep->description,
					$btnedit .
								"&nbsp;&nbsp;&nbsp;" . 
					Html::b($webroot."/report/runreport/".$rep->id," Execute ")
					);
				}
			}
			else {
				// if editor or admin, list all active reports of which i have membership and show approval status and buttons
				$line[] = array(
				$rep->title,
				$app[$rep->is_approved],
				ucfirst($rep->module),
				$rep->getCategoryTitle(),
				$rep->description,
				$btnedit .
							"&nbsp;&nbsp;&nbsp;" . 
				Html::b($webroot."/report/runreport/".$rep->id," Execute ") .
							"&nbsp;&nbsp;&nbsp;" .
				$btndelete,
				);
			}
		}
	}
	else {
		// i am not a member of any reports
		$line[] = array("You have no available reports","","","","","","");
	}
	// populate search dropdowns
	$modules = array();
	$w->ctx("modules",Html::select("module",$modules));
	$category = array();
	$w->ctx("category",Html::select("category",$category));
	$type = array();
	$w->ctx("type",Html::select("type",$type));

	// ser filter dropdown defaults
	$w->ctx("reqModule",$_REQUEST['module']);
	$w->ctx("reqCategory",$_REQUEST['category']);
	$w->ctx("reqType",$_REQUEST['type']);

	// display list of reports, if any
	$w->ctx("viewreports",Html::table($line,null,"tablesorter",true));
}
