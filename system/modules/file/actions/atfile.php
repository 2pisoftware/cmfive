<?php
function atfile_GET(Web &$w) {
	$p = $w->pathMatch("id");
	$id = str_replace(".jpg", "", $p['id']);
	$attachment = $w->service("File")->getAttachment($id);
	$w->sendFile(FILE_ROOT.$attachment->fullpath);
}
