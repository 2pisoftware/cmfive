<?php

Config::set("file", array(
    'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    'search' => array("File Attachments" => "Attachment"),
    "dependencies" => array(
        "knplabs/gaufrette" => "0.1.*"
    ) 
));
