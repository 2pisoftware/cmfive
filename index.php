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

$db_config = array(
		'hostname' => defaultVal(getenv('MYSQL_DB_HOST'),$MYSQL_DB_HOST),
		'username' => defaultVal(getenv('MYSQL_USERNAME'),$MYSQL_USERNAME),
		'password'=> defaultVal(getenv('MYSQL_PASSWORD'),$MYSQL_PASSWORD),
		'database' => defaultVal(getenv('MYSQL_DB_NAME'),$MYSQL_DB_NAME),
		'driver' => 'mysql'
);
$web->db = Crystal::db($db_config);
$web->setModules($modules);
$web->setLogLevel($LOG_LEVEL);
$web->_webroot = "http://".$_SERVER['HTTP_HOST'];
$web->_defaultHandler = "main";

define("WEBROOT", $web->_webroot);

//============== start application =============
$web->start();
