<?php 

error_reporting(E_ALL);

require_once 'system/web.php';
$web = new Web();

//if (!file_exists("config.php")) {
//	$web->install();
//} else {
	//============== start application =============
	$web->start();
//}
