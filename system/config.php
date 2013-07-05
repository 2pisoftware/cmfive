<?php 

//========== System Modules Configuration ===============

$modules['report'] = array(
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['inbox'] = array(
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['admin'] = array(
	'path' => 'system/modules',
	'topmenu' => true,
	'audit_ignore' => array("index"),
);

$modules['help'] = array(
	'path' => 'system/modules',
	'topmenu' => false,
);

$modules['auth'] = array(
	'path' => 'system/modules',
	'topmenu' => false,
);

$modules['file'] = array(
	'path' => 'system/modules',
	'fileroot' => dirname(__FILE__).'/uploads',
    'topmenu' => false,
);

$modules['forms'] = array(
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['wiki'] = array(
	'path' => 'system/modules',
	'topmenu' => true,
);

$modules['task'] = array(
	'path' => 'system/modules',
	'topmenu' => true,
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
