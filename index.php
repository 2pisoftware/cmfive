<?php 
error_reporting(E_ALL ^ E_NOTICE);

require_once "system/db.php";
require_once "system/web.php";

//========== Constants =====================================

define("LIBPATH", str_replace("\\", "/", dirname(__FILE__).'/lib'));
define("SYSTEM_LIBPATH", str_replace("\\", "/", dirname(__FILE__).'/system/lib'));
define("FILE_ROOT", str_replace("\\", "/", dirname(__FILE__)."/uploads/"));
define("MEDIA_ROOT", str_replace("\\", "/", dirname(__FILE__)."/media/"));
define("ROOT", str_replace("\\", "/", dirname(__FILE__)));
define("SESSION_NAME","CM5_SID");

set_include_path(get_include_path() . PATH_SEPARATOR . LIBPATH);
set_include_path(get_include_path() . PATH_SEPARATOR . SYSTEM_LIBPATH);

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

define("WEBROOT", $web->_webroot);

//============== start application =============
$web->start();
