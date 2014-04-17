<?php

Config::set('admin', array(
    'version' => '0.7.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => array("index"),
    'printing' => array(
        'command' => array(
            'unix' => 'lpr $filename',
            'windows' => 'C:\Users\adam\Desktop\SumatraPDF-2.4\SumatraPDF.exe -print-to $printername $filename'
        )
    ),
    "dependencies" => array(
        "swiftmailer/swiftmailer" => "@stable",
        "twig/twig" => "1.*"
    )
));

