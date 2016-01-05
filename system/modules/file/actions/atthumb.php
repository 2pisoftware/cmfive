<?php
function atthumb_GET(Web &$w) {
	list($id) = $w->pathMatch();

	$attachment = $w->File->getAttachment($id);
	$width = $w->request("w",  FileService::$_thumb_width);
	$height = $w->request("h", FileService::$_thumb_height);

	require_once 'phpthumb/ThumbLib.inc.php';
	
	$thumb = PhpThumbFactory::create($attachment->getContent(), [], true);
//	$thumb->resize($width, $height);
	$thumb->adaptiveResize($width, $height);

	header("Content-Type: image/png");
	$thumb->show();
	exit;
}
