<?php
/***************************
 * This page runs test suites as per request parameters
 * and streams HTML output as test run executes
 */
include('lib/class.Diff.php');
include('lib/Mysqldump.php');
include('tests/Spyc.php'); 
// MYSQLDIFF
include_once 'lib/exception.php';
include_once 'lib/dbStruct.php';
include_once 'lib/Source.php';

// output control
define('DS', DIRECTORY_SEPARATOR); 
$folder='';
if (DS=='/') {
	$folder=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']));
} else {
	$folder=str_replace('/','\\',dirname($_SERVER['SCRIPT_FILENAME']));
} 
// config 
include(dirname(dirname($folder)).DS.'tests'.DS.'suites.php');
// ensure dumps folder
						
// MAIN CONTROLLER
if (array_key_exists('key',$_GET) && $_GET['key']==md5('secretfortestingcmfive'.$_GET['keyid'])) {
	if (array_key_exists('savesnapshot',$_GET) ){ 
		saveSnapshot($suites);
	} else if (array_key_exists('listsnapshots',$_GET)) {
		listSnapshots($suites);
//LOAD
	} else if (array_key_exists('loadsnapshot',$_GET) && strlen($_GET['loadsnapshot'])>0) {
		loadSnapshot($suites);
//DOWNLOAD
	} else if (array_key_exists('downloadsnapshot',$_GET) && strlen($_GET['downloadsnapshot'])>0) {
		downloadSnapshot($suites);
//DELETE
	} else if (array_key_exists('deletesnapshot',$_GET) && strlen($_GET['deletesnapshot'])>0) {
		deleteSnapshot($suites);
// RESET DATABASES
	} else if (array_key_exists('checkmysqldiffs',$_GET) && $_GET['checkmysqldiffs']=='1') {
		checkMysqlDiffs($suites);
	} else if (array_key_exists('listmysqldiffs',$_GET) && $_GET['listmysqldiffs']=='1') {
		listMysqlDiffs($suites);
	} else if (array_key_exists('runmysqldiffs',$_GET) && $_GET['runmysqldiffs']=='1') {
		runMysqlDiffs($suites);
	} else if (array_key_exists('resetsystemdatabases',$_GET) && $_GET['resetsystemdatabases']=='1') {
		resetSystemDatabases($suites);
	} else {
		echo "NO ReQUest";
	}
} else {
	echo "Permission denied";
}

function getMysqlDiffs($options,$suite,$safeUpdates=true) {
	$differ = new dbStructUpdater();
	$from_sql=concatenateCmfiveSql($suite['basepath']);
	$dump = new Ifsnop\Mysqldump\Mysqldump($options['name'], $options['user'], $options['pass'],'localhost','mysql',array('add-drop-table' => true,'compress'=>'StdOut'));
	ob_start();
	$dump->start('dummyfilename.sql');
	$to_sql=ob_get_contents();
	ob_end_clean();					
	$diffs = $differ->getUpdates($to_sql, $from_sql);
	// remap to safe alterations
	$diffs2=array();
	if ($safeUpdates) {
		foreach ($diffs as $dk =>$dv) {
			$parts=explode(" ",$dv);
			// drop table
			if (count($parts)>2 && $parts[0]=='DROP' && $parts[1]=='TABLE') {
				$field=str_replace('`',"",$parts[2]);
				if (substr($field,0,10)=="zzz_deleted") {
					$diffs2[]='RENAME TABLE `'.$field.'` TO `zzz_deleted_'.$field.'`';
				}
			} else if (count($parts)>4 && $parts[0]=='ALTER' && $parts[1]=='TABLE' && $parts[3]=='DROP') {
				$field=str_replace('`',"'",$parts[4]);
				$lookup="SHOW COLUMNS FROM ".$parts[2]." WHERE Field=".$field.';';
				$mysqli = new mysqli("localhost", $options['user'], $options['pass'], $options['name']);
				if (mysqli_error($mysqli)) {
					echo "FAIL ".mysqli_error($mysqli);
				}
				$result = mysqli_query($mysqli,$lookup); //implode('\n',$sql));
				while ($ir=$result->fetch_assoc()) {
					if (!$ir) echo $mysqli->error;
					$field=str_replace('`',"",$parts[4]);
					if (substr($field,0,10)=="zzz_deleted") {
						$updateFieldSql='ALTER TABLE '.$parts[2].' CHANGE `'.$field.'` `zzz_deleted_'.$field.'` '.$ir['Type'];
						if ($ir['Null']=='NO') $updateFieldSql.=' NOT NULL ';
						if (strlen($ir['Default'])>0) $updateFieldSql.=' DEFAULT NULL '.$ir['Default'];
						$updateFieldSql.=' '.$ir['Extra'];
						$diffs2[]=$updateFieldSql;
					}
				}
			} else {
				$diffs2[]=$dv;
			}
		}
	} else {
		$diffs2=$diffs;
	}
	return $diffs2;
}
function concatenateCmfiveSql($basePath) {
	// cmfive core sql
	$paths=array($basePath.DS.'system'.DS.'install'.DS.'db.sql',$basePath.DS.'system'.DS.'install'.DS.'dbseed.sql');
	// system modules
	$searchPath=$basePath.DS.'system'.DS.'modules';
	foreach (new DirectoryIterator($searchPath) as $fileInfo) {
		if($fileInfo->isDot()) continue;
		if (is_dir($searchPath.DS.$fileInfo->getFilename()))  {
			if (file_exists($searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'db.sql')) {
				array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'db.sql');
			}
			if (file_exists($searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'dbseed.sql')) {
				array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'dbseed.sql');
			}
		}
	}
	$searchPath=$basePath.DS.'modules';
	foreach (new DirectoryIterator($searchPath) as $fileInfo) {
		if($fileInfo->isDot()) continue;
		if (is_dir($searchPath.DS.$fileInfo->getFilename()))  {
			if (file_exists($searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'db.sql')) {
				array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'db.sql');
			}
			if (file_exists($searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'dbseed.sql')) {
				array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'dbseed.sql');
			}
		}
	}
	$sql=array();
	foreach ($paths as $pk =>$pv) {
		array_push($sql,file_get_contents($pv));
	}
	return implode("\n",$sql);
}

function checkMysqlDiffs($suites) {
	$diffs=array();
	if (strlen($_GET['checkmysqldiffs'])>0) {
		foreach ($suites as $url =>$suite) {
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				if (array_key_exists('basepath',$suite)) {
					$env=array_key_exists('env',$suite)?$suite['env']:'';
					$dbConfig=dbConfigFromCodeception($suite['basepath'],$env);
					$diffs=getMysqlDiffs($dbConfig,$suite);
					if (array_key_exists('mini',$_GET) && strlen($_GET['mini'])>0) {
						if (count($diffs)>0) {
							echo "!!!!";
						}
					} else {
						if (count($diffs)==0) {
							echo "<b>Your database structure is up to date</b>";
						} else {
							echo "<b>Your database structure needs to be updated</b><div id='listmysqldiffs' ><a href='#' class='button tiny' id='listmysqldiffsbutton' >Show Diffs</a></div><div id='runmysqldiffs' ><a href='#' class='button tiny' id='runmysqldiffsbutton' >Update DB with diffs</a></div>";
						}
					}
				}
			}
		}
	}
}

function listMysqlDiffs($suites) {
	$diffs=array();
	if (strlen($_GET['listmysqldiffs'])>0) {
		foreach ($suites as $url =>$suite) {
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				if (array_key_exists('basepath',$suite)) {
					$env=array_key_exists('env',$suite)?$suite['env']:'';
					$dbConfig=dbConfigFromCodeception($suite['basepath'],$env);
					$diffs=getMysqlDiffs($dbConfig,$suite);
					echo "<pre id='mysqldiffs' >".implode(";\n",$diffs)."</pre>";
				}
			}
		}
	}
}

function runMysqlDiffs($suites) {
	$diffs=array();
	if (strlen($_GET['runmysqldiffs'])>0) {
		foreach ($suites as $url =>$suite) {
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				if (array_key_exists('basepath',$suite)) {
					$env=array_key_exists('env',$suite)?$suite['env']:'';
					$dbConfig=dbConfigFromCodeception($suite['basepath'],$env);
					$diffs=getMysqlDiffs($dbConfig,$suite);
					$mysqli = new mysqli("localhost", $dbConfig['user'], $dbConfig['pass'], $dbConfig['name']);
					$result = mysqli_multi_query($mysqli,implode(";\n",$diffs));
					if (mysqli_error($mysqli)) {
						echo "FAIL ".mysqli_error($mysqli);
					} else {
						echo "Updated Database Model ";
					}
					
				}
			}
		}
	}
}

function saveSnapshot($suites) {
	if (strlen($_GET['savesnapshot'])>0) {
		try {
			foreach ($suites as $url =>$suite) {
				//print_r($suite); //$suite,$requestUrl,$url));
				//echo $suite['env'];
				$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
				@mkdir($dumpFolder);
				$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
				if (strpos($requestUrl,$url)!==false) { 
					//echo "suite match";
					if (array_key_exists('env',$suite) && strlen($suite['env'])>0) {
						$dbConfig=dbConfigFromCodeception($suite['basepath'],$suite['env']);
						//print_r($dbConfig);
						$dump = new Ifsnop\Mysqldump\Mysqldump($dbConfig['name'], $dbConfig['user'], $dbConfig['pass'],'localhost','mysql',array('add-drop-table' => true));
						$dump->start($dumpFolder.$_GET['savesnapshot'].'.sql');
						echo "Saved dump to ".$dumpFolder.$_GET['savesnapshot'].'.sql';
					}
				}
			}
		} catch (\Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
		die();
	} else {
		echo "You must provide a name";
	}
}

function listSnapshots($suites) {
	if (strlen($_GET['listsnapshots'])>0) {
		foreach ($suites as $url =>$suite) {
			$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
			@mkdir($dumpFolder);
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				if (array_key_exists('env',$suite) && strlen($suite['env'])>0) {
					foreach (new DirectoryIterator($dumpFolder) as $fileInfo) {
						if($fileInfo->isDot()) continue;
						if (is_file($dumpFolder.DS.$fileInfo->getFilename()))  {
							echo "<div class='snapshotfile' data-filename='".$fileInfo->getFilename()."' >".$fileInfo->getFilename()."&nbsp;&nbsp;&nbsp;<a href='#' class='loadsnapshotbutton button tiny' >Load Snapshot</a>&nbsp;&nbsp;&nbsp;<a href='#' class='downloadsnapshotbutton button tiny' >Download Snapshot</a>&nbsp;&nbsp;&nbsp;<a href='#' class='deletesnapshotbutton button tiny' >Delete Snapshot</a></div>";
						}
					}
				}
			}
		}
	}
}

function loadSnapshot($suites) {
	if (strlen($_GET['loadsnapshot'])>0) {
		foreach ($suites as $url =>$suite) {
			$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				if (is_file($dumpFolder.DS.$_GET['loadsnapshot']))  {
					$basePath=$suite['basepath'];
					$dbConfig=dbConfigFromCodeception($basePath,$suite['env']);
					$mysqli = new mysqli("localhost", $dbConfig['user'], $dbConfig['pass'], $dbConfig['name']);
					$path=$dumpFolder.$_GET['loadsnapshot'];
					if (file_exists($path)) {
						$sql=file_get_contents($path);
						$result = mysqli_multi_query($mysqli,$sql); //implode('\n',$sql));
						if (mysqli_error($mysqli)) {
							echo "FAIL ".mysqli_error($mysqli);
						} else {
							echo "Loaded ".$path;
						}
					}
					
				}
			}
		}
	}
}

function downloadSnapshot($suites) {
	if (strlen($_GET['downloadsnapshot'])>0) {
		foreach ($suites as $url =>$suite) {
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
				$file=$dumpFolder.DS.$_GET['downloadsnapshot'];
				if (is_file($file))  {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename='.basename($file));
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					readfile($file);
					exit;
				}
			}
		}
	}
}

function deleteSnapshot($suites) {
	if (strlen($_GET['deletesnapshot'])>0) {
		foreach ($suites as $url =>$suite) {
			$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			if (strpos($requestUrl,$url)!==false) { 
				$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
				if (is_file($dumpFolder.DS.$_GET['deletesnapshot']))  {
					unlink($dumpFolder.DS.$_GET['deletesnapshot']);
					echo "Deleted ".$dumpFolder.DS.$_GET['deletesnapshot'];
				}
			}
		}
	}
}

function resetSystemDatabases($suites) {
	$found=false;
	foreach ($suites as $url =>$suite) {
		$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		if (strpos($requestUrl,$url)!==false) { 
			if (array_key_exists('basepath',$suite) && strlen($suite['basepath'])>0 && count($suite['paths'])>0) {
				$paths=array();
				$searchPath=$suite['basepath'].DS.'system'.DS.'modules';
				foreach (new DirectoryIterator($searchPath) as $fileInfo) {
					if($fileInfo->isDot()) continue;
					if (is_dir($searchPath.DS.$fileInfo->getFilename()))  {
						array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'droptables.sql');
						array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'db.sql');
						array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'dbseed.sql');
					}
				}
				$searchPath=$suite['basepath'].DS.'modules';
				foreach (new DirectoryIterator($searchPath) as $fileInfo) {
					if($fileInfo->isDot()) continue;
					if (is_dir($searchPath.DS.$fileInfo->getFilename()))  {
						array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'droptables.sql');
						array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'db.sql');
						array_push($paths,$searchPath.DS.$fileInfo->getFilename().DS.'install'.DS.'dbseed.sql');
					}
				}
				//print_r($paths);
				$basePath=$suite['basepath'];
				$dbConfig=dbConfigFromCodeception($basePath,$suite['env']);
				$paths=array_merge(array($basePath.DS.'system'.DS.'tests'.DS.'dropsystemtables.sql',$basePath.DS.'system'.DS.'install'.DS.'db.sql',$basePath.DS.'system'.DS.'install'.DS.'dbseed.sql',$basePath.DS.'system'.DS.'tests'.DS.'userscontactsroles.sql'),$paths);
				$sql='';
				$mysqli = new mysqli("localhost", $dbConfig['user'], $dbConfig['pass'], $dbConfig['name']);
				echo "Imported \n";
				foreach ($paths as $k=>$path) {
					//echo $path.'<br>';
					if (file_exists($path)) {
					//echo "YES<br>";
						$output=array();
						if (file_exists($path)) {
							$sql=file_get_contents($path);
							$result = mysqli_multi_query($mysqli,$sql); //implode('\n',$sql));
							while ($mysqli->more_results() && $ir=$mysqli->next_result()) {if (!$ir) echo $mysqli->error;} // flush multi_queries
							$sql='';
							if (mysqli_error($mysqli)) {
								echo "FAIL ".mysqli_error($mysqli);
							} else {
								echo "OK ";
							}
							echo $path."\n";
						}
					}
				}
				$found=true;
			}
		}
	}
	if (!$found)  {
		echo "No suites or tests configured for ".$url;
	} else {
//s			echo "OK";
	}
	die();
}


 

function dbConfigFromCodeception($path,$env)  {	
	$codeceptionConfig = Spyc::YAMLLoad($path.DS.'tests'.DS.'codeception.master.yml');
	$dbUser='';
	$dbPass='';
	$dbName='';
	if (strlen($env)>0) {
		if (array_key_exists('env',$codeceptionConfig)) {
			$dbUser=$codeceptionConfig['env'][$env]['modules']['config']['Db']['user'];
			$dbPass=$codeceptionConfig['env'][$env]['modules']['config']['Db']['password'];
			$parts=explode("dbname=",$codeceptionConfig['env'][$env]['modules']['config']['Db']['dsn']);
			if (count($parts)>1) {
				$dbName=$parts[1];
			}
		} 
	} else {
		$dbUser=$codeceptionConfig['modules']['config']['Db']['user'];
		$dbPass=$codeceptionConfig['modules']['config']['Db']['password'];
		$parts=explode("dbname=",$codeceptionConfig['modules']['config']['Db']['dsn']);
		if (count($parts)>1) {
			$dbName=$parts[1];
		}
	}
	//return array('ddd','aaa');
	return array('host'=>'localhost	','user'=>$dbUser,'pass'=>$dbPass,'name'=>$dbName);
}
