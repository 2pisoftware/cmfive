<?php
function thumb_GET(Web &$w) {
	$filename = str_replace("..", "", FILE_ROOT.$_REQUEST['path']);
	$w = $w->request("w",150);
	$h = $w->request("h",150);
	require_once 'phpthumb/ThumbLib.inc.php';
	$thumb = PhpThumbFactory::create($filename);
	$thumb->adaptiveResize($w, $h);
	$thumb->show();
	exit;
}
