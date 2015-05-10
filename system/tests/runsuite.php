<?php
/***************************
 * This page runs test suites as per request parameters
 * and streams HTML output as test run executes
 */
// config 
// output control
define('DS', DIRECTORY_SEPARATOR); 
$folder='';
if (DS=='/') {
	$folder=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']));
} else {
	$folder=str_replace('/','\\',dirname($_SERVER['SCRIPT_FILENAME']));
} 
include(dirname(dirname($folder)).DS.'tests'.DS.'suites.php');

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
$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

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
foreach ($suites as $url =>$suite) {
	if (strpos($requestUrl,$url)!==false) { 
		if (array_key_exists('env',$suite) && strlen($suite['env'])>0) {
			$env=' --env '.$suite['env'];
		} 
		foreach ($suite['paths'] as $suiteTitle=>$path) {
			$cmds=array();
			if ($runAllTests==true) {
				$cmds=array('cd '.$path.' && php '.$suite['codeception'].' run  --no-colors --config="'.$path.'"'.$env);
			} else {
				if (array_key_exists($suiteTitle,$requestedTests)) {
					foreach($requestedTests[$suiteTitle] as $rtk => $rtv) {
						array_push($cmds,'cd '.$path.' && php '.$suite['codeception'].' run '.$rtv.' --no-colors --config="'.$path.'"'.$env);
					}
				}
			}
			foreach ($cmds as $cmd) {
				echo $cmd;
				$handle = popen($cmd, "r");
				$detailsTest='';
				$testType='';
				while(!feof($handle)) {
					$buffer = fgets($handle);
					$buffer = trim(htmlspecialchars($buffer));
					$bufferParts=explode(' ',trim($buffer));
					$lastBufferPart=$bufferParts[count($bufferParts)-1];
					// are we starting to run in one of the test type groups - acceptance, init, functional ?
					if (strpos(' '.$buffer,'Acceptance')>0 && strpos(' '.$buffer,'Tests')>0 && strpos(' '.$buffer,'Acceptance') < strpos(' '.$buffer,'Tests')) { 
						$preTitle=substr($buffer,strpos($buffer,'Acceptance'));
						$preTitleParts=explode(" ",$preTitle);
						$envParts=explode("-",$preTitleParts[0]);
						$testType=$envParts[0];
					} else if (strpos(' '.$buffer,'Unit')>0 && strpos(' '.$buffer,'Tests')>0 && strpos(' '.$buffer,'Unit') < strpos(' '.$buffer,'Tests')) {
						$preTitle=substr($buffer,strpos($buffer,'Unit'));
						$preTitleParts=explode(" ",$preTitle);
						$envParts=explode("-",$preTitleParts[0]);
						$testType=$envParts[0];
					} else if (strpos(' '.$buffer,'Functional')>0 && strpos(' '.$buffer,'Tests')>0 && strpos(' '.$buffer,'Functional') < strpos(' '.$buffer,'Tests')) {
						$preTitle=substr($buffer,strpos($buffer,'Functional'));
						$preTitleParts=explode(" ",$preTitle);
						$envParts=explode("-",$preTitleParts[0]);
						$testType=$envParts[0];
					// are we gathering failed test details
					} else if (strlen($detailsTest)>0) {
						if (strpos(' '.$buffer,'---------')==1 ||strpos(' '.$buffer,'FAILURES!')==1 ) {
							$detailsTest='';
							echo '<div class="logitem">'.$buffer . "</div>";
						} else {
							echo '<div class="logitem testdetails" data-testid="'.$suiteTitle.'___'.strtolower($testType).'___'.$detailsTest.'" >';
							echo '<div>'.$buffer.'</div>';
							echo '</div>'."\n";
						}
					// start of gathering failed test results
					} else if (strpos($buffer,'Failed to')!==false) {
						$parts1=explode('(',trim($buffer));
						//print_r($parts1);
						$parts2=explode(' ',trim($parts1[0]));
						$parts3=explode('::',trim($parts2[count($parts2)-1]));
						$testName=$parts3[0];
						$functionName=$parts3[1];
						$logFile=file_get_contents($path.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'_log'.DIRECTORY_SEPARATOR.$testName.'.'.$functionName.'.fail.html');
						
						echo '<div class="logitem logfile" ><div class="reveal-modal" data-reveal data-options="close_on_background_click:true" id="logfile-'.$suiteTitle.'___'.strtolower($testType).'___'.$testName.'___'.$functionName.'">'.$logFile.'</div></div>';
						$detailsTest=$testName.'___'.$functionName;
						echo '<div class="logitem"><b>'.$buffer . "</b></div>\n";
					// test status	
					} else if ($lastBufferPart=="Fail" || $lastBufferPart=="Ok") {
						$parts1=explode('(',$buffer);
						$parts2=explode(')',$parts1[1]);
						$testResult='failed';
						if (trim(strtolower($lastBufferPart))=="ok") {
							$testResult='passed';
						}
						$parts3=explode("::",$parts2[0]);
						$testName=$parts3[0]; 
						$functionName=$parts3[1]; 
						$niceTestName=substr($parts3[0],0,strlen($parts3[0])-4);
						echo '<div class="logitem testresult testresult-'.$testResult.'" data-title="'.$suiteTitle.'" data-suite="'.strtolower($testType).'" data-test="'.$testName.'" data-function="'.$functionName.'" >'.$buffer. "</div>\n";
					// otherwise log it
					} else {
						echo '<div class="logitem">'.$buffer . "</div>";
					}
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
echo "Done";
ob_end_flush();
 ?>
</div>
