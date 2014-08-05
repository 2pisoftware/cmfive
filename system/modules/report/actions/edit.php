<?php

function edit_GET(Web &$w) {
    $p = $w->pathMatch("id");
    $w->Report->navigation($w, (!empty($p['id']) ? "Edit" : "Create") . " Report");
    
    // Get or create report object
    $report = !empty($p['id']) ? $w->Report->getReport($p['id']) : new Report($w);
    if (!empty($p['id']) and empty($report->id)) {
        $w->error("Report not found", "/report");
    }
    
    $w->ctx("report", $report);
    
    // If we're creating this is the table
    $form = array(
        array((!empty($report->id) ? "Edit" : "Create a New") . " Report", "section"),
        array("Title", "text", "title", $report->title),
        array("Module", "select", "module", $report->module, $w->Report->getModules()),
        array("Description", "textarea", "description", $report->description, "110", "2"),
        array("Code", "textarea", "report_code", $report->report_code, "110", "22", "codemirror"),
        array("Connection", "select", "report_connection_id", $report->report_connection_id, $w->Report->getConnections())
    );

    // DB view table
    $db_table = Html::form(array(
        array("Special Parameters", "section"),
        array("User", "static", "user", "{{current_user_id}}"),
        array("Roles", "static", "roles", "{{roles}}"),
        array("Site URL", "static", "webroot", "{{webroot}}"),
        array("View Database", "section"),
        array("Tables", "select", "dbtables", null, $w->Report->getAllDBTables()),
        array(" ", "static", "dbfields", "<span id=dbfields></span>")
    ));
    
    $w->ctx("dbform", $db_table);
    
    
    // Check access rights
    // If user is editing, we need to check multiple things, detailed in the helper function
    if (!empty($report->id)) {
        // Get the report member object for the logged in user
        $member = $w->Report->getReportMember($report->id, $w->Auth->user()->id);
        
        // Check if user can edit this report
        if (!$w->Report->canUserEditReport($report, $member)) {
            $w->error("You do not have access to this report", "/report");
        }
    } else {
        // If we're creating a report, check that the user has rights
        if ($w->Auth->user()->is_admin == 0 and !$w->Auth->user()->hasAnyRole(array('report_admin', 'report_editor'))) {
            $w->error("You do not have create report permissions", "/report");
        }
    }
    
    // Access checked and OK, add approval to form only if is report_admin or admin
    if ($w->Auth->user()->is_admin == 1 or $w->Auth->user()->hasRole("report_admin")) {
        $form[0][] = array("Approved", "checkbox", "is_approved", $report->is_approved);
    }
    
    $w->ctx("report_form", Html::form($form, $w->localUrl("/report/edit/{$report->id}"), "POST", "Save Report"));
    
    // Members tab
    // generate only when editing a report
    
    if (!empty($report->id)) {
        // return list of members of given report
        $members = $w->Report->getReportMembers($report->id);
        // set columns headings for display of members
        $line[] = array("Member","Role","");

        // if there are members, display their full name, role and button to delete the member
        if ($members) {
            foreach ($members as $member) {
                $line[] = array(
                    $w->Report->getUserById($member->user_id),
                    $member->role,
                    Html::box("/report/editmember/".$report->id . "/". $member->user_id," Edit ", true) .
                    Html::box("/report/deletemember/".$report->id."/".$member->user_id," Delete ", true)
                );
            }
        } else {
            // if there are no members, say as much
            $line[] = array("Group currently has no members. Please Add New Members.", "", "");
        }

        // display list of group members
        $w->ctx("viewmembers",Html::table($line,null,"tablesorter",true));
    }
}


function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	
        $report = !empty($p['id']) ? $w->Report->getReport($p['id']) : new Report($w);
        if (!empty($p['id']) && empty($report->id)) {
            $w->error("Report not found", "/report");
        }
        
        // Check access rights
        // If user is editing, we need to check multiple things, detailed in the helper function
        if (!empty($report->id)) {
            // Get the report member object for the logged in user
            $member = $w->Report->getReportMember($report->id, $w->Auth->user()->id);

            // Check if user can edit this report
            if (!$w->Report->canUserEditReport($report, $member)) {
                $w->error("You do not have access to this report", "/report");
            }
        } else {
            // If we're creating a report, check that the user has rights
            if ($w->Auth->user()->is_admin == 0 and !$w->Auth->user()->hasAnyRole(array('report_admin', 'report_editor'))) {
                $w->error("You do not have create report permissions", "/report");
            }
        }

        // Insert or Update
        $report->fill($_POST);
        
        // Force select statements only
        $report->sqltype = "select";
        
        $report_connection_id = $w->request("report_connection_id");
        $report->report_connection_id = intval($report_connection_id);
        $response = $report->insertOrUpdate();
        
        // Handle the response
        if ($response === true) {
            // Add user to report members as owner if this is a new report
            if (empty($p['id'])) {
                $report_member = new ReportMember($w);
                $report_member->report_id = $report->id;
                $report_member->user_id = $w->Auth->user()->id;
                $report_member->role = "OWNER";
                $report_member->insert();
            }
            
            $w->msg("Report " . ($p['id'] ? "updated" : "created"), "/report/edit/{$report->id}");
        } else {
            $w->errorMessage($report, "Report", $response, $p['id'] ? true : false, "/report" . (!empty($account->id) ? "/edit/{$account->id}" : ""));
        }
        
        
        
        
        // OLD CODE - REDUNDANT, KEEPING FOR FEED REFERENCE
        
        
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
