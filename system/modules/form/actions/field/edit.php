<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$_form_field_object = $p['id'] ? $w->Form->getForm($p['id']) : new Form($w);
	
	$form = [
		"Form" => [
			[["Title", "text", "title", $_form_object->title]],
			[["Description", "text", "description", $_form_object->description]],
		]
	];
	
	$w->out(Html::multiColForm($form, '/form-field/edit/' . $_form_object->id));
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$_form_field_object = $p['id'] ? $w->Form->getForm($p['id']) : new Form($w);
	
	$_form_field_object->fill($_POST);
	$_form_field_object->insertOrUpdate();
	
	$w->msg("Form " . ($p['id'] ? 'updated' : 'created'), "/form/show/" . $_form_field_object->form_id);
}
