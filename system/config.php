<?php 

//========== System Modules Configuration ===============

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
);

$modules['task'] = array(
	'active' => true,
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
