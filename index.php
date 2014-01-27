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

//=============== init web.php ==============================

$web->setModules($modules);
$web->setLogLevel($LOG_LEVEL);
$web->_webroot = "http://".$_SERVER['HTTP_HOST'];
$web->_defaultHandler = "main";

define("WEBROOT", $web->_webroot);

//============== start application =============
$web->start();
