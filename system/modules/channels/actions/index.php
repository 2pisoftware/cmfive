<?php

function index_ALL(Web $w) {
	$w->setLayout(null);
	
	$file = new FileParser($w, "dir_scan");
	$result = $file->parseDirectory();
	echo $file->config["path"] . " contains " . $result . " file" . (count($result) !== 1 ? "s" : ""); 
}
