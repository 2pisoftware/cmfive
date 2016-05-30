<?php

Config::set('webdav', array(
	'version' => '0.0.1',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
	'filesystems' => ['','/uploads/attachments'],  // relative to ROOT_PATH
	'availableObjects' => ['Wiki'=>[], 'Tag'=>[], 'TaskGroup'=>[], 'User'=>[] ],
    'dependencies' => array(
        "sabre/dav" => "~3.1.2"
    ),
	'hooks' => array(
		'auth'
	)
));
