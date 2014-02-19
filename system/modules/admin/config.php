<?php


$modules['admin'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => array("index"),
    'printing' => array(
        'command' => array(
            'unix' => 'lpr %filename%',
            'windows' => 'lpr -S %servername% -P %printername% %filename%'
        )
    )
);

