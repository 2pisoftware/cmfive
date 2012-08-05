<?php
function inbox_navigation(Web &$w,$title = null,$nav=null) {
	if ($title) {
		$w->ctx("title",$title);
	}
	$nav = $nav ? $nav : array();
	if ($w->Auth->loggedIn()) {
		$w->menuLink("inbox","New Messages",$nav);
		$w->menuLink("inbox/read","Read Messages",$nav);
		$w->menuLink("inbox/showarchive","Archive",$nav);
		$w->menuLink("inbox/trash","Trash",$nav);
	}
	$w->ctx("navigation", $nav);
}