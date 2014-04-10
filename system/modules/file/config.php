<?php

Config::set('file', array(
	'version' => '0.7.0',
    'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    'search' => array("File Attachments" => "Attachment"),
));
