<?php

function show_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Form not found", "/form");
	}
	
	$_form_object = $w->Form->getForm($p['id']);
	
	$w->ctx("title", "Form: " . $_form_object->printSearchTitle());
	$w->ctx("form", $_form_object);
	$w->ctx("fields", $_form_object->getFields());
}