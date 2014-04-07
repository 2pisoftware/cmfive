<?php

Config::set('channels', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    '__password' => 'maybeconsiderchangingthis',
    'processors' => array(
    	'TestProcessor'
    ),
));
