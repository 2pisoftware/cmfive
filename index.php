<?php 

error_reporting(E_ALL);
//putenv('thisTestRun_testRunnerPath=C:\inetpub\wwwroot\testrunner');
// enable coverage for acceptance testing
$c3File=getenv('thisTestRun_testRunnerPath').DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'c3.php';
if (strlen(getenv('thisTestRun_testRunnerPath'))>0  && file_exists($c3File))  {
	include($cFile);
}

require_once 'system/web.php';
$web = new Web();

//if (!file_exists("config.php")) {
//	$web->install();
//} else {
	//============== start application =============
	$web->start();
//}
