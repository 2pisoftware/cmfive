<?php

function editform_GET(Web $w) {
	$p = $w->pathMatch("application_id","form_id");
	
	$app = $w->Forms->getApplication($p['application_id']);
	if (!$app) {
		if (!$w->request("isbox")) {
			$w->error("This application does not exist.","/forms-admin/index");
		} else {
			echo "This application does not exist.";
		}
	}
	
	// TODO check permissions for editing this form!
	
	$form = $p['form_id'] ? $app->getForm($p['form_id']) : null;
	if ($p['form_id'] && !$form) {
		if (!$w->request("isbox")) {
			$w->error("No such form.","/forms-admin/index");
		} else {
			echo "No such form.";
		}
	}
	if (!$form) {
		$form = new FormsForm($w);
	}
	
	$w->ctx("form",$form);
	$w->ctx("app",$app);
}

function editform_POST(Web $w) {
	$p = $w->pathMatch("app_id","form_id");

	$app = $w->Forms->getApplication($p['app_id']);

	if (!$app) {
		$w->error("This application does not exist.","/forms-admin/index");
	}

	$form = $p['form_id'] ?  $app->getForm($p['form_id']) : null;
	if (!$form) {
		$form = new FormsForm($w);
		$form->application_id = $app->id;
		$msg = "New Form Created.";
	} else {
		// make sure the current user is allowed to edit this app!
		$msg = "Form Updated.";
	}
	
	$form->fill($_POST);
	$form->insertOrUpdate();

	$w->msg($msg,"/forms-admin/viewapplication/".$app->id);
}