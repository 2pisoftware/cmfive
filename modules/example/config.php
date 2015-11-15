<?php
Config::set('example', array(
    'active' => true,
    'path' => 'modules',
    'topmenu' => true,
    'search' => array(
            "Example Data" => "ExampleData",
    ),
    'widgets' => array(),
    'hooks' => array('core_dbobject','example'),
    'processors' => array(),
));
Config::set('testing.module','fred');
