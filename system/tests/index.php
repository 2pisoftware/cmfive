<?php
define('DS', DIRECTORY_SEPARATOR); 
$folder='';
if (DS=='/') {
	$folder=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']));
} else {
	$folder=str_replace('/','\\',dirname($_SERVER['SCRIPT_FILENAME']));
} 
include(dirname(dirname($folder)).DS.'tests'.DS.'suites.php');
// read cm5 config and check testing is enabled
include(dirname(dirname($folder)).DS.'system'.DS.'classes'.DS.'Config.php');
include(dirname(dirname($folder)).DS.'config.php');
if (!config::get('system.enabletesting')) die('Testing is disabled');

// output control
header('Content-Encoding: none;');
set_time_limit(0);
if (ob_get_level() == 0) {
	ob_start();
}
// auto match test suite by request url
$env='';
$keyid=rand(1000,20000);
$key=md5('secretfortestingcmfive'.$keyid);

/*****************************8
 * Recursively copy a folder
 */
function copy_r( $path, $dest )
    {
        if( is_dir($path) )
        {
            @mkdir( $dest );
            $objects = scandir($path);
            if( sizeof($objects) > 0 )
            {
                foreach( $objects as $file )
                {
                    if( $file == "." || $file == ".." )
                        continue;
                    // go on
                    if( is_dir( $path.DS.$file ) )
                    {
                        copy_r( $path.DS.$file, $dest.DS.$file );
                    }
                    else
                    {
                        copy( $path.DS.$file, $dest.DS.$file );
                    }
                }
            }
            return true;
        }
        elseif( is_file($path) )
        {
            return copy($path, $dest);
        }
        else
        {
            return false;
        }
    }
    
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
    
// prep HTML for tabs
function renderSuitesBlock($suites,$key,$keyid) {
	$requestUrl=getRequestUrl();   
	
	$suiteMenu='';
	foreach ($suites as $url =>$suite) {
		
		if (strpos($requestUrl,$url)!==false) { 

			$suiteTests='';
			foreach($suite['paths'] as $suiteName=>$suitePath) {
				
				$suiteTests='';
				$folder='';
				if (DS=='/') {
					$folder=str_replace('\\','/',dirname($_SERVER['SCRIPT_FILENAME']));
				} else {
					$folder=str_replace('/','\\',dirname($_SERVER['SCRIPT_FILENAME']));
				} 
				// COPY SUITE FILES, CREATE SUITE IF IT DOESN'T EXIST
				// COPY MASTER CODECEPTION CONFIG FILE TO THIS TEST SUITE
				copy(dirname(dirname($folder)).DS.'tests'.DS.'codeception.master.yml',$suitePath.DS.'codeception.yml');
				copy_r(dirname(dirname($folder)).DS.'system'.DS.'tests'.DS.'tests',$suitePath.DS.'tests'.DS);
				copy_r(dirname(dirname($folder)).DS.'tests'.DS.'tests',$suitePath.DS.'tests'.DS);
				//@mkdir($folder.DS.'tests'.DS.'_log');
				//$contents=file_get_contents($folder.DS.'tests'.DS.'_bootstrap.php');
				//$bs=str_replace('###BASEPATH###',$folder,$contents);
				//file_put_contents($folder.DS.'tests'.DS.'_bootstrap.php',$bs);
				foreach (array('acceptance','unit','functional') as $ttk => $testType) {
					$count=0;
					foreach (glob($suitePath.DS."tests".DS.$testType.DS."*Cest.php") as $spv) {
						// foreach public test function inside
						$file = file_get_contents ($spv);
						$fileLines=explode("\n",$file);
						//echo $suiteName.' ' .$suitePath.' '.$testType.' '.$spv."<br>";
						foreach($fileLines as $flk => $flv) {
							$lineParts=explode(" ",trim($flv));
							if (count($lineParts)>1 && $lineParts[0]=='class') {
								$className=$lineParts[1];
								$testFileNameParts=explode(".",basename($spv));
								$testFileName=$testFileNameParts[0];
								if (substr($className,strlen($className)-4,4)!='Cest') {
									echo "<div class='testnamewarning' >Class name (".$className.") missing appended Cest </div>";
								}
								if ($testFileName!=$className) {
									echo "<div class='testnamewarning' >Test class name (".$className.") does not match file name in ".$suiteName.' '.$testType."</div>";
								}
							} else if (count($lineParts)>2 && $lineParts[0]=='public' && $lineParts[1]=='function') {
								
								$functionNameParts=explode('(',$lineParts[2]);
								$functionName=trim($functionNameParts[0]);
								if ($functionName!='_before' && $functionName!='_after') {
									
									$testName=substr(basename($spv),0,strlen(basename($spv))-4);
									$status='pending';
									$suiteTests.='<div class="test testresult-'.$status.'" id="'.$suiteName.'___'.$testType.'___'.$testName.'___'.$functionName.'" >'.'<input class="testselected" type="checkbox" checked="checked" />'." <a class='runtestbutton testrunner button tiny' href='dbmanager.php?tests=".$suiteName.'___'.$testType.'___'.$testName.'___'.$functionName."' target='_new' >Run Test</a> ".$testName.' '.$functionName.'</div>';
									$count++;
								}
								
							}
						}
						
					}
				}
				$active=''; //($count>0) ? '' : ' active';
				$suiteMenu.='<li class="accordion-navigation testsuite testresult-pending" data-suite="'.$suiteName.'" >'.'<input class="suiteselected" type="checkbox" checked="checked" /><a href="#testsuite-'.$suiteName.'" > '.$suiteName.'</a><div class="content testresult-pending'.$active.'" id="testsuite-'.$suiteName.'">'."<a class='runtestsuitebutton testrunner button tiny' href='dbmanager.php?tests=".$suiteName."___acceptance' target='_new' >Run Test Suite</a>".$suiteTests.'</div></li>';
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
	<style>
	body.disabled {
		border:4px solid orange;
		background-color: lightgrey;
		opacity: 0.7;
	}
	</style>
</head>	

<body>
	<div class="page-wrap">
		<div class="row">	
			<div class="small-8 columns">	
				<div id='testsuites' >
					<div class='testresult-pending' >
						<h3>Test Results</h3>
						<div id='phperrors' style='display: none;' ></div>
						<input type='hidden' id='md5keyid' value='<?php echo $keyid; ?>'>
						<input type='hidden' id='md5key' value='<?php echo $key; ?>'>
						<a href="#" id='showdbtools' class='button right' data-reveal-id="dbtools">DB Tools<b>...</b></a>

						<div id="dbtools" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
						</div>
						
						
						<div style='display: none' id='warning' >Running tests will modify database structure and data.<br/>
						<b>I understand</b>   <input type='checkbox' id='testsenabled' > </div>
						<a href='#' class='button tiny' id='stopbutton' style='display: none;'  >Stop Tests</a>
						<a href='dbmanager.php' class='button testrunner' id='runbutton'  >Run Tests</a>
						 <br/><a href='#' class='button tiny selectset' id='selectallbutton'  >All</a> <a href='#' class='button tiny selectset' id='selectnonebutton'  >None</a> <a href='#' class='button tiny selectset'  id='selectfailedbutton'  >Failed</a> <a href='#' class='button tiny selectset'  id='selectpendingbutton'  >Pending</a>
							<?php echo renderSuitesBlock($suites,$key,$keyid); ?>	
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
