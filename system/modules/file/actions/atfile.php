<?php
function atfile_GET(Web &$w) {
	$w->setLayout(null);
	list($id) = $w->pathMatch();
	
	$attachment = $w->service("File")->getAttachment($id);
	
	if ($attachment->exists()) {
		$attachment->displayContent();
	}
}
