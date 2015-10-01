<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	
	if (empty($form_id)) {
		$w->error("Form not found", "/form");
	}
	
	$_form_field_object = $p['id'] ? $w->Form->getFormField($p['id']) : new FormField($w);
	
	$form = [
		"Form" => [
			[["Name", "text", "name", $_form_field_object->name]],
			[["Type", "select", "type", $_form_field_object->type, FormField::getFieldTypes()]],
		]
	];
	
	$w->ctx("field", $_form_field_object);
	$w->ctx("form", Html::multiColForm($form, '/form-field/edit/' . $_form_field_object->id . '?form_id=' . $form_id));
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	
	$_form_field_object = $p['id'] ? $w->Form->getFormField($p['id']) : new FormField($w);
	
	$_form_field_object->fill($_POST);
	
	$_form_field_object->form_id = intval($form_id);
	$_form_field_object->insertOrUpdate();
	
	$w->msg("Form " . ($p['id'] ? 'updated' : 'created'), "/form/show/" . $_form_field_object->form_id);
}
