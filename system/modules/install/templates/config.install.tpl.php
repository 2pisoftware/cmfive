// Override Main Module Company Parameters
Config::set('main.application_name', '<?php echo addslashes($config['application_name']); ?>');
Config::set('main.company_name', '<?php echo addslashes($config['company_name']); ?>');
Config::set('main.company_url', '<?php echo addslashes($config['company_url']); ?>');

//=============== Timezone ==================================

date_default_timezone_set('<?php echo $config['timezone']; ?>');

//========== Database Configuration ==========================

Config::set("database", array(
    "hostname"  => "<?php echo $config['db_hostname']; ?>",
    "username"  => "<?php echo $config['db_username']; ?>",
    "password"  => "<?php echo $config['db_password']; ?>",
    "database"  => "<?php echo $config['db_database']; ?>",
    "driver"    => "<?php echo $config['db_driver']; ?>"
));

//=========== Email Layer Configuration =====================

Config::set('email', array(
    "layer"	=> "<?php echo $config['email_layer']; ?>",		// smtp, sendmail
    "host"	=> "<?php echo $config['email_host']; ?>",
    "port"	=> <?php echo intval($config['email_port']); ?>,
    "auth"	=> <?php echo $config['email_auth'] ? 'true' : 'false'; ?>,
    "username"	=> "<?php echo $config['email_username']; ?>",
    "password"	=> "<?php echo $config['email_password']; ?>"
));

Config::set("system.checkCSRF", <?php echo $config['checkCSRF'] ? 'true' : 'false'; ?>);

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
Config::set("system.allow_from_ip", <?php var_export($config['allow_from_ip']); ?>);

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
Config::set('system.rest_api_key', "<?php echo $config['rest_api_key']; ?>");

// exclude any objects that you do NOT want available via REST
// note: only DbObjects which have the $_rest; property are 
// accessible via REST anyway!
Config::set('system.rest_exclude', array(
    "User",
    "Contact",
));
