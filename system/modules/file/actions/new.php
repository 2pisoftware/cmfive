<?php

function new_GET(Web $w) {
	
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	$p = $w->pathMatch("class", "class_id");
	if (empty($p['class']) || empty($p['class_id'])) {
		$w->error(__("Missing class parameters"), $redirect_url);
	}
	
	$_form = [
		__('New Attachment') => [
			[[__("File"), "file", "file"]],
			[[__("Title"), "text", "title"]],
			[[__("Description"), "textarea", "description", "",null,null,'justtext']]
		]
	];
	
	$w->out(Html::multiColForm($_form, "/file/new/" . $p['class'] . '/' . $p['class_id'] . "?redirect_url=" . $redirect_url));
}

function new_POST(Web $w) {
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	$p = $w->pathMatch("class", "class_id");
	if (empty($p['class']) || empty($p['class_id'])) {
		$w->error(__("Missing class parameters"), $redirect_url);
	}
	
	$object = $w->File->getObject($p['class'], $p['class_id']);
	
	if (empty($object->id)) {
		$w->error(__("Object not found"), $redirect_url);
	}
	
	$w->File->uploadAttachment("file", $object, $_POST['title'], $_POST['description'], !empty($_POST['type_code']) ? $_POST['type_code'] : null);
	if(!empty($_POST['file'])) {
		$w->out(json_encode(array('success'=> 'true', 'key' => $_POST['key'])));
	} else {
		$w->msg(__("File attached"), $redirect_url);
	}
}
