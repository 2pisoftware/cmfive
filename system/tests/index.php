<?php
include('suites.php');
// output control
header('Content-Encoding: none;');
set_time_limit(0);
if (ob_get_level() == 0) {
	ob_start();
}
// auto match test suite by request url
$env='';


// prep HTML for tabs
function renderSuitesBlock($suites) {
	$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	$suiteMenu='';
	foreach ($suites as $url =>$suite) {
		if (strpos($requestUrl,$url)!==false) { 
			$suiteTests='';
			foreach($suite['paths'] as $suiteName=>$suitePath) {
				$suiteTests='';
				$folder='';
				if (DIRECTORY_SEPARATOR=='/') {
					$folder=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']));
				} else {
					$folder=str_replace('/','\\',dirname($_SERVER['SCRIPT_FILENAME']));
				} 
				// COPY MASTER CODECEPTION CONFIG FILE TO THIS TEST SUITE
				copy($folder.DIRECTORY_SEPARATOR.'codeception.master.yml',$suitePath.DIRECTORY_SEPARATOR.'codeception.yml');
				
				foreach (array('acceptance','unit','functional') as $ttk => $testType) {
					$count=0;
					foreach (glob($suitePath.DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR.$testType.DIRECTORY_SEPARATOR."*Cest.php") as $spv) {
						// foreach public test function inside
						$file = file_get_contents ($spv);
						$fileLines=explode("\n",$file);
						//echo $suiteName.' ' .$suitePath.' '.$testType.' '.$spv."<br>";
						foreach($fileLines as $flk => $flv) {
							$lineParts=explode(" ",trim($flv));
							if (count($lineParts)>2 && $lineParts[0]=='public' && $lineParts[1]=='function') {
								
								$functionNameParts=explode('(',$lineParts[2]);
								$functionName=trim($functionNameParts[0]);
								if ($functionName!='_before' && $functionName!='_after') {
									
									$testName=substr(basename($spv),0,strlen(basename($spv))-4);
									$status='pending';
									$suiteTests.='<div class="test testresult-'.$status.'" id="'.$suiteName.'___'.$testType.'___'.$testName.'___'.$functionName.'" >'.'<input class="testselected" type="checkbox" checked="checked" />'." <a class='runtestbutton testrunner button tiny' href='runsuite.php?tests=".$suiteName.'___'.$testType.'___'.$testName.'___'.$functionName."' target='_new' >Run Test</a> ".$testName.' '.$functionName.'</div>';
									$count++;
								}
								
							}
						}
						
					}
				}
				$active=''; //($count>0) ? '' : ' active';
				$suiteMenu.='<li class="accordion-navigation testsuite testresult-pending" data-suite="'.$suiteName.'" >'.'<input class="suiteselected" type="checkbox" checked="checked" /><a href="#testsuite-'.$suiteName.'" > '.$suiteName.'</a><div class="content testresult-pending'.$active.'" id="testsuite-'.$suiteName.'">'."<a class='runtestsuitebutton testrunner button tiny' href='runsuite.php?tests=".$suiteName."___acceptance' target='_new' >Run Test Suite</a>".$suiteTests.'</div></li>';
			}
		}
	}
	return '<ul class="accordion" data-accordion>'.
			$suiteMenu."\n".
			'</ul>';
}
?>

<html>
<head>
	<title>CMFIVE Test Suite</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel='stylesheet' href='../templates/js/foundation-5.5.0/css/normalize.css'/>
	<link rel='stylesheet' href='../templates/js/foundation-5.5.0/css/foundation.css'/>
	<link rel="stylesheet" href="app.css" />
	<script src='../templates/js/foundation-5.5.0/js/vendor/modernizr.js'></script>
	<script src='../templates/js/foundation-5.5.0/js/vendor/jquery.js'></script>
	<script src='../templates/js/foundation-5.5.0/js/foundation.min.js'></script>
	<script src='testsuite.js' ></script>
	
</head>	

<body>
	<div class="page-wrap">
		<div class="row">	
			<div class="small-8 columns">	
				<div id='testsuites' >
					<div class='testresult-pending' >
						<h3>Test Results</h3>
						<a href='#' class='button tiny' id='stopbutton' style='display: none;'  >Stop Tests</a>
						<a href='#' class='button tiny testrunner' id='runbutton'  >Run Tests</a>
						 <br/><a href='#' class='button tiny selectset' id='selectallbutton'  >All</a> <a href='#' class='button tiny selectset' id='selectnonebutton'  >None</a> <a href='#' class='button tiny selectset'  id='selectfailedbutton'  >Failed</a> <a href='#' class='button tiny selectset'  id='selectpendingbutton'  >Pending</a>
							<?php echo renderSuitesBlock($suites); ?>	
					</div>
				</div>
			</div>
			<div class="small-4 columns">	
				<h3>Log</h3>
				<div id='log'></div>
			</div>
		</div>
	</div>
<div id='logfiles' style='display: none' ></div>
<script>
// now that testsuites is rendered in DOM
initialisePage();
</script>
</body>
