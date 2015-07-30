<?php

function database_GET(Web $w) {
	$w->ctx("step", 2);

	// Database form
	$form_details = [
		"Database Connection" => [
			[["Driver", "select", "db_driver", $w->request("db_driver", "mysql"), PDO::getAvailableDrivers()]],
			[
				["Hostname", "text", "db_hostname", $w->request("db_hostname", "localhost")],
				["Port", "text", "db_port", $w->request("db_port", "3306")]
			],
			[
				["Username", "text", "db_username", $w->request("db_username")],
				["Password", "password", "db_password", $w->request("db_password")]
			],
			[["Database", "text", "db_database", $w->request("db_database")]]
		]
	];
	
	// Send to template
	$w->ctx("form_details", 
		Html::multiColForm($form_details, $w->localUrl("/install-steps/finish"), "POST", "Import Tables and Data", "install_form", null, null, "_self", true, 
		[
			"db_driver" => ["required"], 
			"db_username" => ["required"], 
			"db_password" => ["required"], 
			"db_port" => ["required"], 
			"db_hostname" => ["required"]
		])
	);
	
}