<?php
define('DS', DIRECTORY_SEPARATOR); 
include('./tests/Spyc.php');
/*
 * This is global bootstrap for autoloading 
 * CONFIG
 */
// parse parameters to extract path (not symlinked source path)
 $env='';
 $basePath='';
 //print_r(array_keys($GLOBALS));
 //die();
 $module='';
 $modulePath='';
 for($i=0; $i< count($GLOBALS['argv']); $i++) {
	 if (strpos(' '.$GLOBALS['argv'][$i],'--config=')>0) {
		$pathParts=explode('config=',$GLOBALS['argv'][$i]);
		$module=basename($pathParts[1]);
		$modulePath=$pathParts[1];
		$basePath=dirname($pathParts[1]).DS;
		if (!is_dir($basePath.'system')) {
			$basePath=dirname(dirname($pathParts[1])).DS;
		}
		if (!is_dir($basePath.'system')) {
			$basePath=dirname(dirname(dirname($pathParts[1]))).DS;
		}
	 } else if (strpos(' '.$GLOBALS['argv'][$i],'--env')>0) {
		 $env=trim($GLOBALS['argv'][$i+1]);
	 }
 }
$codeceptionConfig = Spyc::YAMLLoad('./codeception.yml');
//print_r($codeceptionConfig);

$dbUser='';
$dbPass='';
$dbName='';
if (strlen($env)>0) {
	$dbUser=$codeceptionConfig['env'][$env]['modules']['config']['Db']['user'];
	$dbPass=$codeceptionConfig['env'][$env]['modules']['config']['Db']['password'];
	$dbName=explode("dbname=",$codeceptionConfig['env'][$env]['modules']['config']['Db']['dsn'])[1];
} else {
	$dbUser=$codeceptionConfig['modules']['config']['Db']['user'];
	$dbPass=$codeceptionConfig['modules']['config']['Db']['password'];
	$dbName=explode("dbname=",$codeceptionConfig['modules']['config']['Db']['dsn'])[1];
}

function getModuleTestSql($module,$modulePath) {
	$paths=array();
	array_push($paths,$modulePath.DS.'install'.DS.'droptables.sql');
	array_push($paths,$modulePath.DS.'install'.DS.'db.sql');
	array_push($paths,$modulePath.DS.'install'.DS.'dbseed.sql');
	array_push($paths,$modulePath.DS.'tests'.DS.'_data'.DS.'testusers.sql');
	array_push($paths,$modulePath.DS.'tests'.DS.'_data'.DS.'dump.sql');
	return $paths;
}
$paths=getModuleTestSql($module,$modulePath);

$sql='';
$mysqli = new mysqli("localhost", $dbUser, $dbPass, $dbName);
foreach ($paths as $k=>$path) {
	if (file_exists($path)) {
		$output=array();
		//echo "!=========================================\n";
		echo "\nImport ".$path."\n";
		$sql=file_get_contents($path);
		//echo substr($sql,0,300);
		//echo "!=========================================\n";
		$result = mysqli_multi_query($mysqli,$sql); //implode('\n',$sql));
		while ($mysqli->more_results() && $ir=$mysqli->next_result()) {if (!$ir) echo $mysqli->error;} // flush multi_queries
		$sql='';
	}
}
//print_r($paths);
//echo "done";
