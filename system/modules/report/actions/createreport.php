<?php
//////////////////////////////////////////////////
//			CREATE REPORT						//
//////////////////////////////////////////////////

function createreport_ALL(Web &$w) {
	ReportLib::navigation($w, "Create a Report");

	// get list of  modules
	$modules = $w->Report->getModules();

	// build form to create a report. display to users by role is controlled by the template
	// using lookup with type ReportCategory for category listing
	$f = Html::form(array(
	array("Create a New Report","section"),
	array("Title","text","title", $w->request('title')),
	array("Module","select","module", $w->request('module'), $modules),
	array("Category","select","category", $w->request('category'), lookupForSelect($w, "ReportCategory")),
	array("Description","textarea","description",$w->request('description'),"100","2"),
	array("Code","textarea","report_code",$w->request('report_code'),"100","22"),
	),$w->localUrl("/report/savereport/"),"POST"," Save Report ");

	$t = Html::form(array(
	array("Special Parameters","section"),
	array("User","static","user","{{current_user_id}}"),
	array("Roles","static","roles","{{roles}}"),
	array("Site URL","static","webroot","{{webroot}}"),
	array("View Database","section"),
	array("Tables","select","dbtables",null,$w->Report->getAllDBTables()),
	array(" ","static","dbfields","<span id=dbfields></span>")
	));
	$w->ctx("dbform",$t);

	// display form
	$w->ctx("createreport",$f);
}