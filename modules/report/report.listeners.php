<?php
function report_listener_PRE_ACTION(&$w) {
   	// build Navigation to Reports for current Module
    if ($w->Auth->loggedIn()) {
		$boxes = $w->ctx("boxes");
    	$reports = $w->Report->getReportsforNav();
        	if ($reports) {
				$boxes["Reports"] = Html::ul($reports,null,"menu flt");
           		$w->ctx("boxes",$boxes);
		}
    }
}
