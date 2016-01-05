<?php
function atthumb_GET(Web &$w) {
	list($id) = $w->pathMatch();

	$attachment = $w->File->getAttachment($id);
	$width = $w->request("w",  FileService::$_thumb_width);
	$height = $w->request("h", FileService::$_thumb_height);

	echo $attachment->getFilePath();
	
	require_once 'phpthumb/ThumbLib.inc.php';
	
	$thumb = PhpThumbFactory::create($attachment->getFilePath());
	$thumb->resize($width, $height);
	//$thumb->adaptiveResize($p['w'], $p['h']);
	$thumb->show();
	exit;
}
