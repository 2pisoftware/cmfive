<?php 
error_reporting(E_ALL);

//========= Load System Modules Configuration ===============
require "system/config.php";
$web = new Web();

//========= Load Application Modules Configuration ==========
if (!file_exists("config.php")) {
	$web->install();
} else {
	require "config.php";
	
	//============== start application =============
	$web->start();
}
