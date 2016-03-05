<?php

// criteria/parameter form is submited and report is executed
function exereport_ALL(Web &$w) {
    $w->Report->navigation($w, "Generate Report");
    $p = $w->pathMatch("id");

    $arrreq = array();
    // prepare export buttons for display if format = html
    foreach ($_POST as $name => $value) {
        $arrreq[] = $name . "=" . urlencode($value);
    }

    $viewurl = "/report/edit/" . $p['id'];
    $runurl = "/report/runreport/" . $p['id'] . "/?" . implode("&", $arrreq);
    $repurl = "/report/exereport/" . $p['id'] . "?";
    $strREQ = $arrreq ? implode("&", $arrreq) : "";
    $urlcsv = $repurl . $strREQ . "&format=csv";
    $btncsv = Html::b($urlcsv, "Export as CSV");
    $urlxml = $repurl . $strREQ . "&format=xml";
    $btnxml = Html::b($urlxml, "Export as XML");
    $btnrun = Html::b($runurl, "Edit Report Parameters");
    $btnview = Html::b($viewurl, "Edit Report");
	$btnpdf = Html::b($repurl . $strREQ . "&format=pdf", "Export as PDF");
    $results = "";
    // if there is a report ID in the URL ...
    if (!empty($p['id'])) {
        // get member
        $member = $w->Report->getReportMember($p['id'], $w->session('user_id'));

        // get the relevant report
        $rep = $w->Report->getReportInfo($p['id']);

        // if report exists, execute it
        if (!empty($rep)) {
            $w->Report->navigation($w, $rep->title);
            // prepare and execute the report
            $tbl = $rep->getReportData();

            // if we have an empty return, say as much
            if (!$tbl) {
                $w->error("No Data found for selections. Please try again....", "/report");
            }
            // if an ERROR is returned, say as much
            elseif ($tbl[0][0] == "ERROR") {
                $w->error($tbl[1][0], "/report/runreport/" . $rep->id);
            }
            // if we have records, present them in the requested format
            else {
            	// default to a web page
            	$report_template = $w->Report->getReportTemplate($w->request('template'));
            	
                // Below ifs will no longer work
                $request_format = $w->request('format');
                // as a cvs file for download
                if ($request_format == "csv") {
                    $w->setLayout(null);
                    $w->Report->exportcsv($tbl, $rep->title);
                }
                // as a PDF file for download
                elseif ($request_format == "pdf") {
                    $w->setLayout(null);
                    $w->Report->exportpdf($tbl, $rep->title, $report_template);
                }
                // as XML document for download
                elseif ($request_format == "xml") {
                    $w->setLayout(null);
                    $w->Report->exportxml($tbl, $rep->title);
                }               
                else {
                    $results=$w->Report->generatehtml($tbl,$rep->title,$report_template);
                    // display export and function buttons
                    $w->ctx("exportcsv", $btncsv);
                    $w->ctx("exportxml", $btnxml);
					$w->ctx("exportpdf", $btnpdf);
                    $w->ctx("btnrun", $btnrun);
                    $w->ctx("showreport", $results);

                    // allow editor/admin to edit the report
                    if ((!empty($member->role) && $member->role == "EDITOR") || ($w->Auth->hasRole("report_admin"))) {
                        $w->ctx("btnview", $btnview);
                    }
                }
            }
        } else {
            // report does not exist?
            $w->ctx("showreport", "No such report?");
        }
    }
}
