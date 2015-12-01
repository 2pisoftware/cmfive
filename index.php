<?php 

error_reporting(E_ALL);
putenv('thisTestRun_testRunnerPath=C:\inetpub\wwwroot\testrunner');
// enable coverage for acceptance testing
define('C3_CODECOVERAGE_ERROR_LOG_FILE', 'c:\c3_error.log');
require "c3.php";
//include 'thisTestRun_testRunnerPath').DIRECTORY_SEPARATOR.'staging'.DIRECTORY_SEPARATOR.'c3.php';
/*$c3File=getenv('thisTestRun_testRunnerPath').DIRECTORY_SEPARATOR.'staging'.DIRECTORY_SEPARATOR.'c3.php';
if (strlen(getenv('thisTestRun_testRunnerPath'))>0  && file_exists($c3File))  {
	include($cFile);
	//echo "exists";	
	//die();
} else {
	//echo "no exists";	
	//die();
}*/



require_once 'system/web.php';
$web = new Web();

//if (!file_exists("config.php")) {
//	$web->install();
//} else {
	//============== start application =============
	$web->start();
//}
exit();
