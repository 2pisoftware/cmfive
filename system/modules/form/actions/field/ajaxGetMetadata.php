<?php

function ajaxGetMetadata_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$type = $w->request("type");
	
	if (empty($p['id']) && empty($type)) {
		header("HTTP/1.1 404 Not Found");
		return;
	}
	
	$field = null;
	if (!empty($p['id'])) {
		$field = $w->Form->getFormField($p['id']);
		if(empty($field->id)) {
			header("HTTP/1.1 404 Not Found");
			return;
		}
	
		$instance = $field->instance_class;
		if ($instance::respondsTo($type)) {
			echo Html::form($instance::metadataForm($type));
		}
	} else {
		if(empty($type)) {
			header("HTTP/1.1 404 Not Found");
			return;
		}
		
		$instances = Config::get('form.instances');
		if (!empty($instances)) {
			foreach($instances as $instance) {
				if ($instance::respondsTo($type)) {
					echo Html::form($instance::metadataForm($type));
				}
			}
		}
	}
	
}