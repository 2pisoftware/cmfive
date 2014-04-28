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
    'database' => array(
        'output' => 'sql', // To backup to XML, you need to add -X after 'mysqldump' i.e. 'mysqldump -X ...'
        'command' => array(
            'unix' => 'mysqldump -u $username -p$password $dbname | > gzip $filename',
            'windows' => 'J:\\xampp\\mysql\\bin\\mysqldump.exe -u $username -p$password $dbname > $filename'
        ),
        'backuplocations' => array(
            'dropbox' => array(
                'key' => 'ovqyh81xiocztij',
                'secret' => 'nqqf9hfhsic4p10'
            )
        )
    ),
    "dependencies" => array(
        "swiftmailer/swiftmailer" => "@stable",
        "twig/twig" => "1.*"
    )
));

