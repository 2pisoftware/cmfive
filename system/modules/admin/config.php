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
    )
));
