<?php
class TaskLib {
	
	static function task_navigation(&$w,$title = null,$nav=null) {
		if ($title) {
			$w->ctx("title",$title);
		}
	
		$nav = $nav ? $nav : array();
	
		if ($w->Auth->loggedIn()) {
			$w->menuLink("task/createtask","New Task",$nav);
			$w->menuLink("task/index","Task Dashboard",$nav);
			$w->menuLink("task/tasklist","Task List",$nav);
			$w->menuLink("task/tasklist","Notifications",$nav);
			$w->menuLink("task/taskweek","Activity",$nav);
			$w->menuLink("task-group/viewtaskgrouptypes","Task Groups",$nav);
		}
		$w->ctx("navigation", $nav);
	}
	
}