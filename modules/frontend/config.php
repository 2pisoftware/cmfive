<?php
Config::set('frontend', array(
'active' => true,
'path' => 'modules',
'topmenu' => false,
));

/*
 * You will need to set up a domain route to this module to be used as 
 * a frontend system.
 */

Config::set('domain.route',array(
 // domain => module
	"frontend.dev" => "frontend",
));

// bypass authentication for the frontend modules
Config::set("system.allow_module", Config::get("system.allow_module") + array(
	"frontend",
	"frontend-catalog"
));
/*
 * You can setup more than one front facing website on different domains
 * served from different modules
 */

// Config::set('domain.route',array(
//  // domain => module
// 	"mydomain.com" => "mydomain",
// 	"otherdomain.com" => "otherdomain"
// ));
