<?php


$modules['admin'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => array("index"),
    'printing' => array(
        'command' => array(
            'unix' => 'lpr $filename',
            'windows' => 'C:\Users\adam\Desktop\SumatraPDF-2.4\SumatraPDF.exe -print-to $printername $filename'
        )
    )
);

