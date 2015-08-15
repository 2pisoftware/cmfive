<?php
/***************************
 * This page runs test suites as per request parameters
 * and streams HTML output as test run executes
 */
include('lib/class.Diff.php');
include('lib/Mysqldump.php');
include('tests/Spyc.php'); 

// output control
define('DS', DIRECTORY_SEPARATOR); 
$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
$folder='';
if (DS=='/') {
	$folder=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']));
} else {
	$folder=str_replace('/','\\',dirname($_SERVER['SCRIPT_FILENAME']));
} 
// config 
include(dirname(dirname($folder)).DS.'tests'.DS.'suites.php');
// read cm5 config and check testing is enabled
include(dirname(dirname($folder)).DS.'system'.DS.'classes'.DS.'Config.php');
include(dirname(dirname($folder)).DS.'config.php');
if (!config::get('system.enabletesting')) die('Testing is disabled');


// MAIN CONTROLLER
if (array_key_exists('key',$_GET) && $_GET['key']==md5('secretfortestingcmfive'.$_GET['keyid'])) {
// RUN TESTS	
		runTests($suites,$requestUrl);
} else {
	echo "Permission denied";
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
		foreach ($suites as $url =>$suite) {
			if (strpos($requestUrl,$url)!==false) { 
				$phpLog=tailCustom($suite,30);
				$env='';
				if (array_key_exists('env',$suite) && strlen($suite['env'])>0) {
					$env=' --env '.$suite['env'];
				} 
				$verbosity='';
				if (array_key_exists('v',$_GET)) {
					//$verbosity=' -vvv ';
				} 
				foreach ($suite['paths'] as $suiteTitle=>$path) {
					$cmds=array();
					if ($runAllTests==true && false) {
						$cmds=[];
						//array_push($cmds,'cd '.$path.' && php '.$suite['codeception'].' clean &&');
						array_push($cmds,'cd '.$path.' && php '.$suite['codeception'].' run  '.$verbosity.'--no-colors --config="'.$path.'"'.$env);
					} else {
						if (array_key_exists($suiteTitle,$requestedTests)) {
							foreach($requestedTests[$suiteTitle] as $rtk => $rtv) {
								array_push($cmds,'cd '.$path.' && php '.$suite['codeception'].' run '.$rtv.' '.$verbosity.'--no-colors --config="'.$path.'"'.$env);
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
							// are we starting to run in one of the test type groups - acceptance, init, functional ?
							if ($errorActive)  {
								if (strlen(trim($buffer))>0)  {
								//	echo "<div class='phperror' >".$buffer."</div>";
								}
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
								}
								echo '<div class="logitem"><b>'.$buffer . "</b></div>\n";
								
							// test status	
							} else if ((count($bufferParts)>1 && $lastBufferPart=="Ok") || $lastBufferPart=="Fail" ) {
								//print_r($bufferParts);
								$parts1=explode('(',$buffer);
								if (count($parts1)>1) {
									$parts2=explode(')',$parts1[1]);
									$testResult='passed';
									if (array_search('[F]',$bufferParts)!==false || array_search('Fail',$bufferParts)!==false) {
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
		ob_end_flush();	
} ?>
