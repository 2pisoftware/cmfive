<?php
//////////////////////////////////////////////////
//			VIEW REPORT							//
//////////////////////////////////////////////////

function viewreport_GET(Web &$w) {
	ReportLib::navigation($w, "Edit Report");
	$p = $w->pathMatch("id");

	// tab: view report
	// if there is a report ID in the URL ...
	if ($p['id']) {
		// get member
		$member = $w->Report->getReportMember($p['id'],$w->session('user_id'));

		// get the relevant report
		$rep = $w->Report->getReportInfo($p['id']);

		// if the report exists check status & role before displaying
		if ($rep) {
			// if ($w->Auth->user()->hasRole("report_user") && (!$w->Auth->user()->hasRole("report_editor")) && (!$w->Auth->user()->hasRole("report_admin"))) {
			if (($member->role != "EDITOR") && (!$w->Auth->user()->hasRole("report_admin"))) {
				// redirect the unauthorised
				$w->msg("Sorry, you are not authorised to edit this Report","/report/index/");
			}
			elseif ($w->Auth->user()->hasRole("report_admin")) {
				// build the report for edit WITH an Approval checkbox
				// using lookup with type ReportCategory for category listing

				// get list of modules
				$modules = $w->Report->getModules();

				$f = Html::form(array(
				array("Report Definition","section"),
				array("Title","text","title", $rep->title),
				array("Module","select","module", $rep->module, $modules),
				//array("Category","select","category", $rep->category, lookupForSelect($w, "ReportCategory")),
				array("Description","textarea","description",$rep->description,110,2,false),
				array("Code","textarea","report_code",$rep->report_code,110,22,false),
				array("Approved","checkbox","is_approved", $rep->is_approved),
				),$w->localUrl("/report/editreport/".$rep->id),"POST"," Update Report ");

				// provide a button by which the report may be tested, ie. executed
				$btntestreport = Html::b("/report/runreport/".$rep->id," Test the Report ");
				$w->ctx("btntestreport",$btntestreport);
					
				// create form providing view of tables and fields
				$t = Html::form(array(
				array("Special Parameters","section"),
				array("User","static","user","{{current_user_id}}"),
				array("Roles","static","roles","{{roles}}"),
				array("Site URL","static","webroot","{{webroot}}"),
				array("View Database","section"),
				array("Tables","select","dbtables",null,$w->Report->getAllDBTables()),
				array("Fields","static","dbfields","<span id='dbfields'></span>")
				));
				$w->ctx("dbform",$t);
			}
			//			elseif ($w->Auth->user()->hasRole("report_editor")) {
			elseif ($member->role == "EDITOR") {
				// build the report for edit. edited forms again require approval
				// using lookup with type ReportCategory for category listing

				// get list of modules
				$modules = $w->Report->getModules();

				$f = Html::form(array(
				array("Create a New Report","section"),
				array("Title","text","title", $rep->title),
				array("Module","select","module", $rep->module, $modules),
				array("Category","select","category", $rep->category, lookupForSelect($w, "ReportCategory")),
				array("Description","textarea","description",$rep->description,100,2,true),
				array("Code","textarea","report_code",$rep->report_code,100,22,false),
				array("","hidden","is_approved", "0"),
				),$w->localUrl("/report/editreport/".$rep->id),"POST"," Update Report ");

				// create form providing view of tables and fields
				$t = Html::form(array(
				array("Special Parameters","section"),
				array("Logged in User","static","user","{{current_user_id}}"),
				array("User Roles","static","roles","{{roles}}"),
				array("Site URL","static","webroot","{{webroot}}"),
				array("View Database","section"),
				array("Tables","select","dbtables",null,$w->Report->getAllDBTables()),
				array("Fields","static","dbfields","<span id='dbfields'></span>")
				));
				$w->ctx("dbform",$t);
			}
			else {
				// redirect on all other occassions
				$w->msg($rep->title . ": Report has yet to be approved","/report/index/");
			}
		}
		else {
			$f = "Report does not exist";
		}
	}
	else {
		$f = "Report does not exist";
	}
	// return the form for display and edit
	$w->ctx("viewreport",$f);

	$btnrun = Html::b("/report/runreport/".$rep->id, " Execute Report ");
	$w->ctx("btnrun",$btnrun);


	// tab: view members
	// see report.lib.php
	ReportLib::viewMemberstab($w, $p['id']);
}
