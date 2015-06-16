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

Config::set('system.password_salt', 'override this in your project config');

//========= REST Configuration ==============================
// check the following configuration carefully to secure
// access to the REST infrastructure.

// use the API_KEY to authenticate with username and password
Config::set('system.rest_api_key', "abcdefghijklmnopqrstuvwxyz1234567890");

// include class of objects that you want available via REST
Config::set('system.rest_include', array(
	// "Contact"
));


//======== Pass through authentication ===========
// Passtrough authentication currently only configured to work with LDAP and IIS
Config::set('system.use_passthrough_authentication', false);

// Config::set("system.ldap", array(
//     'host'          => '192.168.0.256', // Host name or IP of LDAP server
//     'port'          => 389, // 389 is default
//     'username'      => 'DOMAIN\\User',
//     'password'      => 'password',
//     'domain'        => 'domain.example.com',
//     'base_dn'       => 'DC=domain,DC=EXAMPLE,DC=COM',
//     'auth_ou'       => 'OU=Users',
//     'auth_search'   => '(cn={$username})', // {username} will be replaced in auth
//     'search_filter_attribute' => array(), // Here you can specify only certain attributes to get from ldap such as "ou" or "cn" etc
// ));