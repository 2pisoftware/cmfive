<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	if (empty($p['id'])) {
		$w->error("Missing attachment ID", $redirect_url);
	}
	
	$attachment = $w->File->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error("Attachment not found", $redirect_url);
	}
	
	$_form = [
		'Edit Attachment' => [
			[["Title", "text", "title", $attachment->title]],
			[["Description", "textarea", "description", $attachment->description,null,null,'justtext']]
		]
	];
	
	$w->out(Html::multiColForm($_form, "/file/edit/" . $attachment->id . "?redirect_url=" . $redirect_url));
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	if (empty($p['id'])) {
		$w->error("Missing attachment ID", $redirect_url);
	}
	
	$attachment = $w->File->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error("Attachment not found", $redirect_url);
	}
	
	$attachment->title = $_POST['title'];
	$attachment->description = $_POST['description'];
	$attachment->update();
	
	$w->msg("Attachment updated", $redirect_url);
	
}