<?php

Config::set('task', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'search' => array('Tasks' => "Task"),
    'hooks' => array(
        'core_web',
        'core_dbobject',
        'comment',
        'attachment'
    ),
    'ical' => array(
        'send' => false
    )
));
