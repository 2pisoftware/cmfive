<?php

// ========= Session ========================
ini_set('session.gc_maxlifetime', 60 * 60 * 6);

//========== Constants =====================================
define("CMFIVE_VERSION", "0.8.0");

define("ROOT_PATH", str_replace("\\", "/", getcwd()));
define("SYSTEM_PATH", str_replace("\\", "/", getcwd() . '/system'));

define("LIBPATH", str_replace("\\", "/", getcwd() . '/lib'));
define("SYSTEM_LIBPATH", str_replace("\\", "/", getcwd() . '/system/lib'));
define("FILE_ROOT", str_replace("\\", "/", getcwd() . "/uploads/")); // dirname(__FILE__)
define("MEDIA_ROOT", str_replace("\\", "/", dirname(__FILE__) . "/../media/"));
define("ROOT", str_replace("\\", "/", dirname(__FILE__)));
define("SESSION_NAME", "CM5_SID");

set_include_path(get_include_path() . PATH_SEPARATOR . LIBPATH);
set_include_path(get_include_path() . PATH_SEPARATOR . SYSTEM_LIBPATH);

require_once "system/db.php";
require_once "system/web.php";

//========= Check CSRF Token ================================
Config::set("system.checkCSRF", true);

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
Config::set("system.allow_from_ip", array());

// or bypass authentication for the following modules
Config::set("system.allow_module", array(
    // "rest", // uncomment this to switch on REST access to the database objects. Tread with CAUTION!
));

Config::set('system.allow_action', array(
    "auth/login",
    "auth/forgotpassword",
    "auth/resetpassword",
    "admin/datamigration"
));

//========= REST Configuration ==============================
// check the following configuration carefully to secure
// access to the REST ifnrastructure.

// use the API_KEY to authenticate with username and password
Config::set('system.rest_api_key', "abcdefghijklmnopqrstuvwxyz1234567890");

// exclude any objects that you do NOT want available via REST
// note: only DbObjects which have the $_rest; property are 
// accessible via REST anyway!
Config::set('system.rest_exclude', array(
    "User",
    "Contact",
));
