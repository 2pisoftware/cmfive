<?php

function form_core_template_tab_headers(Web $w, $object) {
	if (empty($object)) {
		return;
	}
	
	// Check and see if there are any forms mapped to the object
	if ($w->Form->areFormsMappedToObject($object)) {
		return "<a href='#form'>Form</a>";
	}
	return '';
}

function form_core_template_tab_content(Web $w, $params) {
	if (empty($params['object']) || empty($params['redirect_url'])) {
		return;
	}
	
	// Check and see if there are any forms mapped to the object
	$forms = $w->Form->getFormsMappedToObject($params['object']);
	
	$forms_list = '<div id="form">';
	if (!empty($forms)) {
		foreach($forms as $form) {
			$forms_list .= $w->partial("listform", [
				"form" => $form, 
				"redirect_url" => $params['redirect_url'], 
				'object' => $params['object']
			], "form");
		}
	}
	return $forms_list . '</div>';
}
