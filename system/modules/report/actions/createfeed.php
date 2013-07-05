<?php
function createfeed_GET(Web &$w) {
	ReportLib::navigation($w, "Create a Feed");

	// get list of reports for logged in user. sort to list unapproved reports first
	$reports = $w->Report->getReportsbyUserId($w->session('user_id'));

	// if i am a member of a list of reports, lets display them
	if (($reports) && ($w->Auth->user()->hasRole("report_editor")  || $w->Auth->user()->hasRole("report_admin"))) {
		foreach ($reports as $report) {
			// get report data
			$rep = $w->Report->getReportInfo($report->report_id);
			$myrep[] = array($rep->title, $rep->id);
		}
	}

	$f = Html::form(array(
	array("Create a Feed from a Report","section"),
	array("Select Report","select","rid",null,$myrep),
	array("Feed Title","text","title"),
	array("Description","textarea","description",null,"40","6"),
	),$w->localUrl("/report/createfeed/"),"POST"," Save ");

	$w->ctx("createfeed",$f);
}

function createfeed_POST(Web &$w) {
	ReportLib::navigation($w, "Create a Feed");

	// create a new feed
	$feed = new ReportFeed($w);

	$arr["report_id"] = $_REQUEST["rid"];
	$arr["title"] = $_REQUEST["title"];
	$arr["description"] = $_REQUEST["description"];
	$arr["dt_created"] = date("d/m/Y");
	$arr["is_deleted"] = 0;

	$feed->fill($arr);
	$feed->insert();

	$rep = $w->Report->getReportInfo($feed->report_id);

	// if report exists
	if ($rep) {
		// get the form array
		$elements = $rep->getReportCriteria();

		if ($elements) {
			foreach ($elements as $element) {
				if (($element[0] != "Description") && ($element[2] != ""))
				$query .= $element[2] . "=&lt;value&gt;&";
			}
		}

		$query = rtrim($query,"&");

		$feedurl = $w->localUrl("/report/feed/?key=" . $feed->key . "&" . $query);

		$feed->url = $feedurl;
		$feed->update();

		$feedurl = "<b>Your Feed has been created</b><p>Use the URL below, with actual query parameter values, to access this report feed.<p>" . $feedurl;
		$w->ctx("feedurl", $feedurl);
	}
}