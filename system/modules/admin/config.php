<?php

Config::set('admin', array(
    'version' => '0.8.0',
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
    'database' => array(
        'command' => array(
            'unix' => 'mysqldump -u $username -p$password $dbname | > gzip $filename.gz',
            'windows' => 'J:\\xampp\\mysql\\bin\\mysqldump.exe -u $username -p$password $dbname > $filename'
        )
    ),
    "dependencies" => array(
        "swiftmailer/swiftmailer" => "@stable",
        "twig/twig" => "1.*"
    )
));

