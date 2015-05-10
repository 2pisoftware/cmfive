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
 for($i=0; $i< count($GLOBALS['argv']); $i++) {
	 if (strpos(' '.$GLOBALS['argv'][$i],'--config=')>0) {
		$pathParts=explode('config=',$GLOBALS['argv'][$i]);
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
if (strlen($env)>0) {
	$dbUser=$codeceptionConfig['env'][$env]['modules']['config']['Db']['user'];
	$dbPass=$codeceptionConfig['env'][$env]['modules']['config']['Db']['password'];
	$dbName=explode("dbname=",$codeceptionConfig['env'][$env]['modules']['config']['Db']['dsn'])[1];
} else {
	$dbUser=$codeceptionConfig['modules']['config']['Db']['user'];
	$dbPass=$codeceptionConfig['modules']['config']['Db']['password'];
	$dbName=explode("dbname=",$codeceptionConfig['modules']['config']['Db']['dsn'])[1];
}
$paths=array(
	// various install scripts
	$basePath.'system'.DS.'tests'.DS.'droptables.sql',
	$basePath.'system'.DS.'install'.DS.'db.sql',
	$basePath.'system'.DS.'install'.DS.'dbseed.sql',
	$basePath.'modules'.DS.'crm'.DS.'install'.DS.'db.sql',
	$basePath.'modules'.DS.'staff'.DS.'install'.DS.'db.sql',
	$basePath.'system'.DS.'modules'.DS.'favorites'.DS.'install'.DS.'install.sql',
	// shared testing data
	$basePath.'system'.DS.'tests'.DS.'userscontactsroles.sql'
);
$sql='';
$mysqli = new mysqli("localhost", $dbUser, $dbPass, $dbName);
foreach ($paths as $k=>$path) {
	$output=array();
	echo "Import ".$path."\n";
	$sql=file_get_contents($path);
	$result = mysqli_multi_query($mysqli,$sql); //implode('\n',$sql));
	
	while ($mysqli->more_results() && $ir=$mysqli->next_result()) {if (!$ir) echo $mysqli->error;} // flush multi_queries
	$sql='';
	
}
//echo "done";
