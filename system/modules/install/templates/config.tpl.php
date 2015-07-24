// Override Main Module Company Parameters
Config::set('main.application_name', '<?php echo addslashes($application_name); ?>');
Config::set('main.company_name', '<?php echo addslashes($company_name); ?>');
Config::set('main.company_url', '<?php echo addslashes($company_url); ?>');

//=============== Timezone ==================================

date_default_timezone_set('<?php echo $timezone; ?>');

//========== Database Configuration ==========================

Config::set("database", array(
    "hostname"  => "<?php echo $db_hostname; ?>",
    "username"  => "<?php echo $db_username; ?>",
    "password"  => "<?php echo $db_password; ?>",
    "database"  => "<?php echo $db_database; ?>",
    "driver"    => "<?php echo $db_driver; ?>"
));

//=========== Email Layer Configuration =====================

Config::set('email', array(
    "layer"	=> "<?php echo $email_layer; ?>",		// smtp, sendmail
    "host"	=> "<?php echo $email_host; ?>",
    "port"	=> <?php echo intval($email_port); ?>,
    "auth"	=> <?php echo $email_auth ? 'true' : 'false'; ?>,
    "username"	=> "<?php echo $email_username; ?>",
    "password"	=> "<?php echo $email_password; ?>"
));

Config::set("system.checkCSRF", <?php echo $checkCSRF ? 'true' : 'false'; ?>);

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
Config::set("system.allow_from_ip", <?php var_export($allow_from_ip); ?>);

// or bypass authentication for the following modules
Config::set("system.allow_module", array(
    // "rest", // uncomment this to switch on REST access to the database objects. Tread with CAUTION!
));

Config::set('system.allow_action', array(
    "auth/login",
    "auth/forgotpassword",
    "auth/resetpassword",
    //"admin/datamigration"
));
//========= REST Configuration ==============================
// check the following configuration carefully to secure
// access to the REST infrastructure.

// use the API_KEY to authenticate with username and password
Config::set('system.rest_api_key', "<?php echo $rest_api_key; ?>");

// exclude any objects that you do NOT want available via REST
// note: only DbObjects which have the $_rest; property are 
// accessible via REST anyway!
Config::set('system.rest_exclude', array(
    "User",
    "Contact",
));
