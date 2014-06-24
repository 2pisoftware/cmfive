<?php
//////////////////////////////////////////////////
//			EDIT REPORT							//
//////////////////////////////////////////////////

function editreport_POST(Web $w) {
	$p = $w->pathMatch("id");
	 
	if (!array_key_exists("is_approved",$_REQUEST))
	$_REQUEST['is_approved'] = 0;

	// if there is a report ID in the URL ...
	if ($p['id']) {
		// get report details
		$rep = $w->Report->getReportInfo($p['id']);

		// if report exists, update it
		if ($rep) {
			$_POST['sqltype'] = $w->Report->getSQLStatementType($_POST['report_code']);
			$rep->fill($_POST);
                        $rep->report_connection_id = intval($_POST["report_connection_id"]);
			$rep->update();
			$repmsg = "Report updated.";

			// check if there is a feed associated with this report
			$feed = $w->Report->getFeedInfobyReportId($rep->id);
			if ($feed) {
				// if feed exists, need to reevaluate the URL in case of changes in the report parameters
				$elements = $rep->getReportCriteria();

				if ($elements) {
					foreach ($elements as $element) {
						if (($element[0] != "Description") && ($element[2] != ""))
						$query .= $element[2] . "=&lt;value&gt;&";
					}
				}

				$query = rtrim($query,"&");

				// use existing key to reevaluate feed URL
				$feedurl = $w->localUrl("/report/feed/?key=" . $feed->key . "&" . $query);

				// update feed URL
				$feed->url = $feedurl;
				$feed->update();
			}
		}
		else {
			$repmsg = "Report does not exist";
		}
	}
	else {
		$repmsg = "Report does not exist";
	}

	// return
	$w->msg($repmsg,"/report/viewreport/".$rep->id);
}
