<?php

function database_GET(Web $w) {
	$w->ctx("step", 2);

	// Database form
	$form_details = [
		"Admin User Account" => [
			[
				["First name", "text", "admin_firstname", $w->request("admin_firstname")],
				["Last name", "text", "admin_lastname", $w->request("admin_lastname")],
				["Email", "text", "admin_email", $w->request("admin_email")]
			],
			[
				["Username", "text", "admin_username", $w->request("admin_username")],
				["Password", "password", "admin_password"]
			]
		],
		"Database Connection" => [
			[["Driver", "select", "db_driver", $w->request("db_driver", "mysql"), PDO::getAvailableDrivers()]],
			[
				["Hostname", "text", "db_host", $w->request("db_host", "localhost")],
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
			"db_hostname" => ["required"],
			"admin_username" => ["required"],
			"admin_password" => ["required"],
			"admin_first_name" => ["required"],
			"admin_last_name" => ["required"],
			"admin_email" => ["required"]
		])
	);
}
