<?php

Config::set('main', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'http://github.com/careck/cmfive',
    "dependencies" => array(
        "codeception/codeception" => "*",
        "monolog/monolog" => "1.8.*@dev"
    )
));
