<?php 
require "config.php";
require_once "system/db.php";
require_once "system/web.php";

error_reporting(E_ALL ^ E_NOTICE);

//=============== Timezone ======================
date_default_timezone_set('Australia/Sydney');

//=============== init web.php ==================
$web = new Web();
$db_config = array(
        'hostname' => defaultVal(getenv('MYSQL_DB_HOST'),'localhost'),
        'username' => defaultVal(getenv('MYSQL_USERNAME'),'root'),
        'password'=> defaultVal(getenv('MYSQL_PASSWORD'),''),
        'database' => defaultVal(getenv('MYSQL_DB_NAME'),'cmfive'),
        'driver' => 'mysql'
);

$web->db = Crystal::db($db_config);
$web->setModules($modules);
$web->setLogLevel("debug");
$web->_webroot = "http://".$_SERVER['HTTP_HOST'];

define("WEBROOT", $web->_webroot);

//============== start application =============
$web->start();
