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
        "codeception/codeception" => "2.0.14",
        "monolog/monolog" => "1.8.*@dev",
   	"site5/phantoman" => "*",
        "jakoch/phantomjs-installer" => "*"
    )
));
