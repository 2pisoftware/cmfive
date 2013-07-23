<?php 

//========== Constants =====================================

define("LIBPATH", str_replace("\\", "/", dirname(__FILE__).'/lib'));
define("SYSTEM_LIBPATH", str_replace("\\", "/", dirname(__FILE__).'/system/lib'));
define("FILE_ROOT", str_replace("\\", "/", dirname(__FILE__)."/uploads/"));
define("MEDIA_ROOT", str_replace("\\", "/", dirname(__FILE__)."/media/"));
define("ROOT", str_replace("\\", "/", dirname(__FILE__)));
define("SESSION_NAME","CM5_SID");

set_include_path(get_include_path() . PATH_SEPARATOR . LIBPATH);
set_include_path(get_include_path() . PATH_SEPARATOR . SYSTEM_LIBPATH);

require_once "system/db.php";
require_once "system/web.php";

//========== System Modules Configuration ===============

$modules['main'] = array(
		'active' => true,
		'path' => 'system/modules',
		'topmenu' => false,
		'application_name' => 'cmfive',
		'company_name' => 'cmfive',
		'company_url' => 'http://github.com/careck/cmfive',
);

$modules['report'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['inbox'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['admin'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
	'audit_ignore' => array("index"),
);

$modules['help'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => false,
);

$modules['auth'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => false,
);

$modules['file'] = array(
	'active' => true,
	'path' => 'system/modules',
	'fileroot' => dirname(__FILE__).'/uploads',
    'topmenu' => false,
);

$modules['forms'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['wiki'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
	'search' => array("WikiPage" => "Wiki Pages")
);

$modules['task'] = array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
	'search' => array('Task' => "Tasks")
);

/**
 * This is how an external module would be defined
 */
/*
$modules['externaltest'] = array(
	'topmenu' => true,
	'path' => dirname(__FILE__).'/../cmfive_modules',
);
*/
