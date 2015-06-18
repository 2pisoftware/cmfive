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
function getRequestUrl() {
	$requestScheme='http';
	//  IIS
	if (array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS']=='on') {
		$requestScheme='https';
	// apache
	} else if (array_key_exists('REQUEST_SCHEME',$_SERVER) && $_SERVER['REQUEST_SCHEME']=='https') {
		$requestScheme='https';
	}
	return $requestScheme.'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
}
$requestUrl=getRequestUrl();

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
		saveSnapshot($suites,$_GET['savesnapshot']);
	} else if (array_key_exists('listsnapshots',$_GET)) {
		listSnapshots($suites);
//LOAD
	} else if (array_key_exists('loadsnapshot',$_GET) && strlen($_GET['loadsnapshot'])>0) {
		loadSnapshot($suites,$_GET['loadsnapshot']);
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
		runTests($suites,$requestUrl);
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
			$requestUrl=getRequestUrl();
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
			$requestUrl=getRequestUrl();
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
			$requestUrl=getRequestUrl();
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

function saveSnapshot($suites,$snapFile) {
	if (strlen($snapFile)>0) {
		try {
			foreach ($suites as $url =>$suite) {
				//print_r($suite); //$suite,$requestUrl,$url));
				//echo $suite['env'];
				$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
				@mkdir($dumpFolder);
				$requestUrl=getRequestUrl();
				if (strpos($requestUrl,$url)!==false) { 
					if (array_key_exists('env',$suite) && strlen($suite['env'])>0) {
						$dbConfig=dbConfigFromCodeception($suite['basepath'],$suite['env']);
						//print_r($dbConfig);
						$dump = new Ifsnop\Mysqldump\Mysqldump($dbConfig['name'], $dbConfig['user'], $dbConfig['pass'],'localhost','mysql',array('add-drop-table' => true));
						$dump->start($dumpFolder.$snapFile.'.sql');
						echo "Saved dump to ".$dumpFolder.$snapFile.'.sql';
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
			$requestUrl=getRequestUrl();
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

function loadSnapshot($suites,$snapFile) {
	if (strlen($_GET['loadsnapshot'])>0) {
		foreach ($suites as $url =>$suite) {
			$dumpFolder=$suite['basepath'].DS.'tests'.DS.'dumps'.DS;
			$requestUrl=getRequestUrl();
			if (strpos($requestUrl,$url)!==false) { 
				if (is_file($dumpFolder.DS.$snapFile))  {
					$basePath=$suite['basepath'];
					$dbConfig=dbConfigFromCodeception($basePath,$suite['env']);
					$mysqli = new mysqli("localhost", $dbConfig['user'], $dbConfig['pass'], $dbConfig['name']);
					$path=$dumpFolder.$snapFile;
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
			$requestUrl=getRequestUrl();
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
			$requestUrl=getRequestUrl();
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
		$requestUrl=getRequestUrl();
		if (strpos($requestUrl,$url)!==false) { 
			if (array_key_exists('basepath',$suite) && strlen($suite['basepath'])>0) {
				$env=array_key_exists('env',$suite) ? $suite['env'] : '';
				$dbConfig=dbConfigFromCodeception($suite['basepath'],$env);
				$sql=concatenateCmfiveSql($suite['basepath']);
				
				$pdo = new PDO('mysql:dbname='.$dbConfig['name'], $dbConfig['user'], $dbConfig['pass']);

				try {
					$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

					$query = "SELECT concat('DROP TABLE IF EXISTS ', table_name, ';')
							  FROM information_schema.tables
							  WHERE table_schema = '".$dbConfig['name']."'";

					foreach($pdo->query($query) as $row) {
						//echo $row[0];
						$pdo->exec($row[0]);
					}

					$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
					$pdo->exec($sql);
					echo "Database tables dropped and recreated";
				} catch (Exception $e) {
					echo "FAIL ";
					var_dump($e);
				}
				
				/*$result = mysqli_multi_query($mysqli,$sql); //implode('\n',$sql));
				while ($mysqli->more_results() && $ir=$mysqli->next_result()) {if (!$ir) echo $mysqli->error;} // flush multi_queries
				$sql='';
				if (mysqli_error($mysqli)) {
					echo "FAIL ".mysqli_error($mysqli);
				} else {
					echo "Database tables dropped and recreated";
				}*/
//				echo $sql;
			}
		}
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



function tailCustom($suite, $lines = 1, $adaptive = true) {
	if (array_key_exists('phpLogFile',$suite) && file_exists($suite['phpLogFile']))  {
		$filepath=$suite['phpLogFile'];
		// Open file
		$f = @fopen($filepath, "rb");
		if ($f === false) return false;

		// Sets buffer size
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		// Jump to last character
		fseek($f, -1, SEEK_END);

		// Read it and adjust line number if necessary
		// (Otherwise the result would be wrong if file doesn't end with a blank line)
		if (fread($f, 1) != "\n") $lines -= 1;
		
		// Start reading
		$output = '';
		$chunk = '';

		// While we would like more
		while (ftell($f) > 0 && $lines >= 0) {

			// Figure out how far back we should jump
			$seek = min(ftell($f), $buffer);

			// Do the jump (backwards, relative to where we are)
			fseek($f, -$seek, SEEK_CUR);

			// Read a chunk and prepend it to our output
			$output = ($chunk = fread($f, $seek)) . $output;

			// Jump back to where we started reading
			fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

			// Decrease our line counter
			$lines -= substr_count($chunk, "\n");

		}

		// While we have too many lines
		// (Because of buffer size we might have read too many)
		while ($lines++ < 0) {

			// Find first newline and remove all text before that
			$output = substr($output, strpos($output, "\n") + 1);

		}

		// Close file and return
		fclose($f);
		return trim($output);

	}
}

function runTests($suites,$requestUrl) {
		$testSections=array();			
		header('Content-Encoding: none;');
		set_time_limit(0);
		if (ob_get_level() == 0) {
			ob_start();
		}
		?>
		<script>
		if (typeof updatePage=='undefined') {
			var updatePage = function () {};
		}
		</script>
		<div id='output'  >

		<?php
		
		$requestedTests=array();
		$runAllTests=false;
		if (array_key_exists('tests',$_GET) && strlen($_GET['tests'])>0)  {
			$testParam=array_key_exists('tests',$_GET) ? $_GET['tests'] : '' ;
			//echo $testParam;
			$selectedTests=explode(",",$testParam);
			foreach($selectedTests as $k=>$testString) {
				$testParts=explode("___",$testString);
				if (!array_key_exists($testParts[0],$requestedTests)) {
					$requestedTests[$testParts[0]]=array();
				}
				$testsToRun='';
				if (array_key_exists(1,$testParts)) {
					$testsToRun.=' '.$testParts[1];
				}
				if (array_key_exists(2,$testParts)) {
					$testsToRun.=' '.$testParts[2];
				}
				if (array_key_exists(3,$testParts)) {
					$testsToRun.=':'.$testParts[3];
				}
				array_push($requestedTests[$testParts[0]],$testsToRun);
			}
		} else {
			$runAllTests=true;
		}
		$phpLog=null;
		$snapFile='tests'.DS.'dumps'.DS.'testrunnerdump-'.rand().'.sql';
		foreach ($suites as $url =>$suite) {
			// url match
			if (strpos($requestUrl,$url)!==false) { 
				if (array_key_exists('preservedb',$suite) && strlen($suite['preservedb'])>0) {
					saveSnapshot($suites,$snapfile);
				}
				$phpLog=tailCustom($suite,30);
				$env='';
				if (array_key_exists('env',$suite) && strlen($suite['env'])>0) {
					$env=' --env '.$suite['env'];
				} 
				$verbosity='';
				if (array_key_exists('v',$_GET)) {
					$verbosity=' -vv --steps';
				} 
				foreach ($suite['paths'] as $suiteTitle=>$path) {
					$cmds=array();
					$php=array_key_exists('php',$suite) ? '"'.$suite['php'].'"' : 'php';
					
					if ($runAllTests==true) {
						$cmds=array('cd '.$path.' && '.$php.' '.$suite['codeception'].' run  --no-colors --config="'.$path.'"'.$env.' '.$verbosity);
					} else {
						if (array_key_exists($suiteTitle,$requestedTests)) {
							foreach($requestedTests[$suiteTitle] as $rtk => $rtv) {
								array_push($cmds,'cd '.$path.' && '.$php.' '.$suite['codeception'].' run '.$rtv.' --no-colors --config="'.$path.'"'.$env.' '.$verbosity);
							}
						}
					}
					foreach ($cmds as $cmd) {
						echo $cmd;
						$handle = popen($cmd, "r");
						$detailsTest='';
						$errorActive=false;
						$testType='';
						while(!feof($handle)) {
							$buffer = fgets($handle);
							$buffer = trim(htmlspecialchars($buffer));
							$bufferParts=explode(' ',trim($buffer));
							$lastBufferPart='';
							if (count($bufferParts)>0) {
								$lastBufferPart=$bufferParts[count($bufferParts)-1];
							}
							$secondLastBufferPart='';
							if (count($bufferParts)>1) {
								$secondLastBufferPart=$bufferParts[count($bufferParts)-2];
							}
							if ($errorActive)  {
								if (strlen(trim($buffer))>0)  {
									echo "<div class='phperror' >".$buffer."</div>";
								}
							// are we starting to run in one of the test type groups - acceptance, init, functional ?
							} else if (strpos(' '.$buffer,'Acceptance')>0 && strpos(' '.$buffer,'Tests')>0 && strpos(' '.$buffer,'Acceptance') < strpos(' '.$buffer,'Tests')) { 
								$preTitle=substr($buffer,strpos($buffer,'Acceptance'));
								$preTitleParts=explode(" ",$preTitle);
								$envParts=explode("-",$preTitleParts[0]);
								$testType=$envParts[0];
								echo '<div class="logitem"><i>'.$buffer . "</i></div>";
							} else if (strpos(' '.$buffer,'Unit')>0 && strpos(' '.$buffer,'Tests')>0 && strpos(' '.$buffer,'Unit') < strpos(' '.$buffer,'Tests')) {
								$preTitle=substr($buffer,strpos($buffer,'Unit'));
								$preTitleParts=explode(" ",$preTitle);
								$envParts=explode("-",$preTitleParts[0]);
								$testType=$envParts[0];
								echo '<div class="logitem"><i>'.$buffer . "</i></div>";
							} else if (strpos(' '.$buffer,'Functional')>0 && strpos(' '.$buffer,'Tests')>0 && strpos(' '.$buffer,'Functional') < strpos(' '.$buffer,'Tests')) {
								$preTitle=substr($buffer,strpos($buffer,'Functional'));
								$preTitleParts=explode(" ",$preTitle);
								$envParts=explode("-",$preTitleParts[0]);
								$testType=$envParts[0];
								echo '<div class="logitem"><i>'.$buffer . "</i></div>";
							} else if (strpos(' '.$buffer,'Parse error:')>0) { //strpos($buffer,'Call Stack:')!==false || 
								echo "<div class='phperror' >";
								echo $buffer;
								echo "</div>";
								$errorActive=true;
							} else if (strpos($buffer,'Call Stack:')!==false) {
								//$errorActive=false;
							// are we gathering failed test details
							} else if (strlen($detailsTest)>0) {
								if (strpos(' '.$buffer,'---------')==1 ||strpos(' '.$buffer,'FAILURES!')==1 ) {
									$detailsTest='';
									echo '<div class="logitem">'.$buffer . "</div>";
								} else {
									$thisTestType='';
									$dtParts=explode('___',$detailsTest);
									if (count($dtParts)>1) {
										$testName=$dtParts[0];
										if (array_key_exists($testName,$testSections)) {
											$thisTestType=$testSections[$testName];
											echo '<div class="logitem testdetails" data-testid="'.$suiteTitle.'___'.strtolower($thisTestType).'___'.$detailsTest.'" >';
											echo '<div>'.$buffer.'</div>';
											echo '</div>'."\n";
										}
									}
								}
							// start of gathering failed test results
							} else if (strpos($buffer,'Failed to')!==false) {
								$parts1=explode('(',trim($buffer));
								//print_r($parts1);
								$parts2=explode(' ',trim($parts1[0]));
								$parts3=explode('::',trim($parts2[count($parts2)-1]));
								$testName=$parts3[0];
								$functionName=$parts3[1];
								if (file_exists($path.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'_output'.DIRECTORY_SEPARATOR.$testName.'.'.$functionName.'.fail.html')) {
									$logFile=file_get_contents($path.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'_output'.DIRECTORY_SEPARATOR.$testName.'.'.$functionName.'.fail.html');
									$thisTestType='';
									if (array_key_exists($testName,$testSections)) {
										$thisTestType=$testSections[$testName];
									}
									echo '<div class="logitem logfile" ><div class="reveal-modal" data-reveal data-options="close_on_background_click:true" id="logfile-'.$suiteTitle.'___'.strtolower($thisTestType).'___'.$testName.'___'.$functionName.'">'.$logFile.'</div></div>';
									$detailsTest=$testName.'___'.$functionName;
								} else {
									echo '<div class="logitem"><b>'.$buffer . "</b></div>\n";
								}
								
							// test status	
							} else if ((count($bufferParts)>1 && $lastBufferPart=="Ok") || $lastBufferPart=="Fail"|| $lastBufferPart=="Error" ) {
								//print_r($bufferParts);
								$parts1=explode('(',$buffer);
								if (count($parts1)>1) {
									$parts2=explode(')',$parts1[1]);
									$testResult='passed';
									if (array_search('[F]',$bufferParts)!==false || array_search('Fail',$bufferParts)!==false || array_search('Error',$bufferParts)!==false) {
										$testResult='failed';
									}
									$parts3=explode("::",$parts2[0]);
									$testName=$parts3[0]; 
									$functionName='';
									if (count($parts3)>1) {
										$functionName=$parts3[1]; 
									}
									$testSections[$testName]=$testType;
									//die();
									if (count($suiteTitle)>0 && count($testType)>0) {
										echo '<div class="logitem testresult testresult-'.$testResult.'" data-title="'.$suiteTitle.'" data-suite="'.strtolower($testType).'" data-test="'.$testName.'" data-function="'.$functionName.'" >'.$buffer. "</div>\n";
										echo '<div class="logitem"><i>'.$buffer . "</i></div>";
									} else {
										echo '<div class="logitem"><b><i>'.$buffer . "</i></b)</div>";
									}
								} else {
									echo '<div class="logitem">'.$buffer . "</div>";
								}
							// otherwise log it
							} else {
								echo '<div class="logitem">'.$buffer . "</div>";
							}
						//	echo '<div class="dumpme">'.$buffer . "</div>";

							//echo "<script>updatePage();</script>";
							echo str_pad('', 4096);    
							ob_flush();
							flush();
							sleep(0.3);
						}
						pclose($handle);
					}
				}
				//$acceptanceConfig = Spyc::YAMLLoad($suite['path'].'/tests/acceptance.suite.yml');
				break;
			}
		}
		$phpLogNow=tailCustom($suite,30);
		if (strlen($phpLog)>0 && strlen($phpLogNow)>0) {
			$diff = Diff::compare($phpLog,$phpLogNow);
			$lines=array();
			if (count($diff[Diff::INSERTED])>0) {
				foreach($diff as $dk =>$dv) {
					if ($dv[1]==Diff::INSERTED) {
						if (strlen(trim($dv[0]))>0) $lines[]=$dv[0];
					}
				}
				if (count($lines)>0)  {
					echo "<div class='phperrorlog' >";
					echo implode("<br>\n",$lines);
					echo "</div>";
				}
			}
		}
		echo "Done</div>";
		if (array_key_exists('preservedb',$suite) && strlen($suite['preservedb'])>0) {
			loadSnapshot($suites,$snapfile);
		}
		
		ob_end_flush();	
}
