<?php

Config::set("admin", array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => array("index"),
    'database_backup' => true,
    'printing' => array(
        'command' => array(
            'unix' => 'lpr $filename',
            'windows' => '/Path/to/SumatraPDF.exe -print-to $printername $filename'
        )
    ),
    'database' => array(
        'command' => array(
            'unix' => 'mysqldump -u $username -p$password $dbname | gzip > $filename.gz',
            'windows' => 'C:\xampp\mysql\bin\mysqldump.exe -u $username -p$password $db_name > $filename'
        )
    ),
    "dependencies" => array(
        "swiftmailer/swiftmailer" => "@stable",
        "twig/twig" => "1.*"
    )
));

