<?php 
error_reporting(E_ALL);

//========= Load System Modules Configuration ===============
require "system/config.php";

//========= Load Application Modules Configuration ==========
require "config.php";

//=============== init web.php ==============================
$web = new Web();
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
