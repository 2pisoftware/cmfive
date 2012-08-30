<?php
//========== Constants =========================

define("LIBPATH", str_replace("\\", "/", dirname(__FILE__).'/lib'));
define("FILE_ROOT", str_replace("\\", "/", dirname(__FILE__)."/uploads/"));
define("MEDIA_ROOT", str_replace("\\", "/", dirname(__FILE__)."/media/"));
define("ROOT", str_replace("\\", "/", dirname(__FILE__)));
define("SESSION_NAME","CM5_SID");
set_include_path(get_include_path() . PATH_SEPARATOR . LIBPATH);

//========== Module Configuration ===============

$modules['main'] = array(
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'http://github.com/careck/cmfive',
);
$modules['report'] = array(
    'topmenu' => true,
);
$modules['admin'] = array(
    'topmenu' => true,
	'audit_ignore' => array("index"),
);

$modules['help'] = array(
    'topmenu' => false,
);

$modules['auth'] = array(
    'topmenu' => false,
);
$modules['file'] = array(
    'fileroot' => dirname(__FILE__).'/uploads',
    'topmenu' => false,
);
$modules['forms'] = array(
    'topmenu' => true,
);

$modules['wiki'] = array(
    'topmenu' => true,
);
$modules['task'] = array(
    'topmenu' => true,
);

/**
 * This is how an external module would be defined
 */
/*
$modules['externaltest'] = array(
	'topmenu' => true,
	'path' => dirname(__FILE__).'/../cmfive_modules/externaltest',
);
*/

