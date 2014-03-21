<?php
function forms_listener_PRE_ACTION(&$w) {

	// insert side menu
	
	if ($w->Auth->loggedIn() && $w->currentModule()=="forms") {
		$nav = array();
	
		$w->menuLink("forms/index","Applications",$nav);
		
		// show all applications in the menu
		$apps = $w->Forms->getApplications();
		if ($apps) {
			foreach ($apps as $app) {
				$w->menuLink("forms/app/".$app->slug,$app->title,$nav);
			}
		}
		$w->ctx("navigation", $nav);
		
		$boxes = $w->ctx("boxes");
		
		// show admin menu to admin user only
		if ($w->Auth->hasRole("forms_admin")) {
			$adm = array();
			$w->menuLink("forms-admin/index","Edit Applications",$adm);
			$boxes["Forms Admin"] = Html::ul($adm,null,"side-nav");
		}
	
		$w->ctx("boxes",$boxes);
	}
	
}
