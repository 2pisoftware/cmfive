<?php

// ========= Session ========================
ini_set('gc_maxlifetime', 60 * 60 * 6);

//========== Constants =====================================
define("CMFIVE_VERSION", "0.5.0");

define("LIBPATH", str_replace("\\", "/", dirname(__FILE__) . '/lib'));
define("SYSTEM_LIBPATH", str_replace("\\", "/", dirname(__FILE__) . '/system/lib'));
define("FILE_ROOT", str_replace("\\", "/", dirname(__FILE__) . "/uploads/"));
define("MEDIA_ROOT", str_replace("\\", "/", dirname(__FILE__) . "/media/"));
define("ROOT", str_replace("\\", "/", dirname(__FILE__)));
define("SESSION_NAME", "CM5_SID");

set_include_path(get_include_path() . PATH_SEPARATOR . LIBPATH);
set_include_path(get_include_path() . PATH_SEPARATOR . SYSTEM_LIBPATH);

require_once "system/db.php";
require_once "system/web.php";

//========== System Modules Configuration ===============

$modules['main'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'http://github.com/careck/cmfive',
);

$modules['report'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
);

$modules['inbox'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
);

$modules['admin'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => array("index"),
);

$modules['help'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
);

$modules['auth'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'search' => array("Contacts" => "Contact"),
);

$modules['file'] = array(
    'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    'search' => array("File Attachments" => "Attachment"),
);

$modules['forms'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
);

$modules['search'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
);

$modules['wiki'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'search' => array("Wiki Pages" => "WikiPage")
);

$modules['task'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'search' => array('Tasks' => "Task")
);

$modules['rest'] = array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
);
