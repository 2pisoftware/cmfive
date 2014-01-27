<?php
class ReportLib {
	// build the Report navigation
	static function navigation(&$w,$title = null,$nav=null) {
		if ($title) {
			$w->ctx("title",$title);
		}
	
		$nav = $nav ? $nav : array();
	
		if ($w->Auth->loggedIn()) {
			$w->menuLink("report/index","Report Dashboard",$nav);
	
			if ($w->Auth->user()->hasRole("report_editor") || $w->Auth->user()->hasRole("report_admin")) {
				$w->menuLink("report/createreport","Create a Report",$nav);
				$w->menuLink("report/listconnections","Connections",$nav);
				$w->menuLink("report/listfeed","Feeds Dashboard",$nav);
			}
		}
	
		$w->ctx("navigation", $nav);
	}
	
	static function viewMemberstab(Web &$w, $id) {
		// return list of members of given report
		$members = $w->Report->getReportMembers($id);
		// get report details
		$report = $w->Report->getReportInfo($id);
	
		// set columns headings for display of members
		$line[] = array("Member","Role","");
	
		// if there are members, display their full name, role and button to delete the member
		if ($members) {
			foreach ($members as $member) {
				$line[] = array(
				$w->Report->getUserById($member->user_id),
				$member->role,
				Html::box("/report/editmember/".$report->id . "/". $member->user_id," Edit ", true) .
					"&nbsp;&nbsp;" . 
				Html::box("/report/deletemember/".$report->id."/".$member->user_id," Delete ", true)
				);
			}
		}
		else {
			// if there are no members, say as much
			$line[] = array("Group currently has no members. Please Add New Members.", "", "");
		}
	
		$w->ctx("reportid",$report->id);
	
		// display list of group members
		$w->ctx("viewmembers",Html::table($line,null,"tablesorter",true));
	}	
}