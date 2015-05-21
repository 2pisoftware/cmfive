<?php 
error_reporting(E_ALL);



//========= Load System Modules Configuration ===============
require "system/config.php";
$web = new Web();

//========= Load Application Modules Configuration ==========
if (!file_exists("config.php")) {
	echo "<b>No config.php found. Please copy config.php.example, change parameters as necessary and rename to config.php<b>";
	die();
}
require "config.php";

//============== start application =============
$web->start();
