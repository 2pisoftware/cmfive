<?php

Config::set('main', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'http://github.com/careck/cmfive',
    "dependencies" => array(
        "codeception/codeception" => "*"
    )
));
