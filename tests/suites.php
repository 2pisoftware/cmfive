<?php
/****
 * This file contains configuration of test suites in dev and test environments
 * and a mapping of test request url to determine environment.
 */
 
/*
 * Return an array of paths to tests with basePath prepended
 * Add modules to be tested here
 */ 
function getSuitePaths($basePath) {
	return array(
		'tasks'=>$basePath.''.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'task',
		//'frontend'=>$basePath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'frontend',
		'admin'=>$basePath.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'admin'
	);
} 
function getCodeceptionPath($basePath) {
	return $basePath.DIRECTORY_SEPARATOR."system".DIRECTORY_SEPARATOR."composer".DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."codeception".DIRECTORY_SEPARATOR."codeception".DIRECTORY_SEPARATOR."codecept";
}

/*$linuxConfig=array(
	'basepath'=>'/var/www/projects/cmfiveclean/dev',
	'paths' =>getSuitePaths('/var/www/projects/cmfiveclean/dev'),
	'codeception' =>'/var/www/projects/cmfive/dev/system/composer/vendor/codeception/codeception/codecept',
	'env'=>'test'
);
$testConfig=array_merge(array('basepath'=>'/var/www/projects/cmfiveclean/dev','paths'=>getSuitePaths('/var/www/projects/cmfiveclean/dev')),$linuxConfig);
*/

// BASE CONFIG TO BE COPIED AND MODIFIED
$wampConfig=array(
	// location of php log file
	'phpLogFile'=>'c:\wamp\logs\php_error.log'
);
// hack from base config here	
$steveDevConfig=array_merge(
	array(
	// base install path
	'basepath'=>'c:\wamp\www\cmfiveclean',
	// inject paths matching basepath
	'paths'=>getSuitePaths('c:\wamp\www\cmfiveclean'),
	// location of codeception executable phar
	'codeception' =>getCodeceptionPath('c:\wamp\www\cmfiveclean'),
	// mapping to codeception environment for variable db connection and site URL.
	'env'=>'devsteve'
),$wampConfig);



// MAPPING OF URLS TO TEST CONFIGS
$suites=array(
	// dev steve
	'http://cmfive.steve'=>$steveDevConfig,
	// test site
	//'http://cmfive.dev.code.2pisoftware.com/' =>$testConfig,
);
