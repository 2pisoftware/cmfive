<?php

function editapplication_GET(Web $w) {
	$p = $w->pathMatch("id");
	
	$app = $w->Forms->getApplication($p['id']);
	
	if (!$app) {
		$app = new FormsApplication($w);
		$w->setTitle("Create Application");
	} else {
		// make sure the current user is allowed to edit this app!
		$w->setTitle("Edit Application");
	}
	
	$w->ctx("app",$app);
}

function editapplication_POST(Web $w) {
	$p = $w->pathMatch("id");
	
	$app = $w->Forms->getApplication($p['id']);
	
	if (!$app) {
		$app = new FormsApplication($w);
		$msg = "New Application Created.";
	} else {
		// make sure the current user is allowed to edit this app!
		$msg = "Application Updated.";
	}
	
	$app->fill($_POST);
	$app->insertOrUpdate();
	
	$w->msg($msg,"/forms-admin/index");	
}