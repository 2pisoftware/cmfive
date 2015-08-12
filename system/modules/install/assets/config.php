//======= Override Main Module Company Parameters ============

Config::set('main.application_name', '2pi CRM');
Config::set('main.company_name', '2pi Software');
Config::set('main.company_url', 'http://2pisoftware.com');

// enter a valid email address

Config::set('main.company_support_email','adam@2pisoftware.com');

//=============== Timezone ===================================

date_default_timezone_set('Australia/Sydney');

//========== Database Configuration ==========================

Config::set('database', array(
    "hostname"  => "",
    "username"  => "crm2pi",
    "password"  => "crm2pi",
    "database"  => "crm2pi",
    "driver"    => "mysql"
));

//=========== Email Layer Configuration =====================

Config::set('email', [
    "layer"	=> "STMP",		// smtp or sendmail
    "command" => "",		// used for sendmail layer only
    "host"	=> "smtp.mandrillapp.com",
	"port"	=> 465,
	"auth"	=> 1,
    "username"	=> 'adam@2pisoftware.com',
    "password"	=> 'wzTN9gU-NKrpBiuI0ALU0A',
]);

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
// specify an IP address and an array of allowed actions from that IP

Config::set("system.allow_from_ip", []);

// or bypass authentication for the following modules

Config::set("system.allow_module", []);

// or bypass authentication for the following actions

Config::set('system.allow_action', [
    "auth/login",
    "auth/forgotpassword",
    "auth/resetpassword",
	"install-steps/finish"
]);

//========= REST Configuration ==============================
// check the following configuration carefully to secure
// access to the REST infrastructure.
//===========================================================

// use the API_KEY to authenticate with username and password

Config::set('system.rest_api_key', "");

// include class of objects that you want available via REST
// be aware that only the listed objects will be available via
// the REST API

Config::set('system.rest_include', []);