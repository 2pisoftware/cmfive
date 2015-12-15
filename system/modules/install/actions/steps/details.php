<?php

function details_GET(Web $w) {
	$w->ctx("step", 1);
	
	$timezones = array();
	foreach(timezone_abbreviations_list() as $timezone_group) {
		foreach($timezone_group as $timezone) {
			$timezones[$timezone['timezone_id']] = [$timezone['timezone_id'], $timezone['timezone_id']];
		}
	}
	
	asort($timezones);
	
	// Database form
	$form_details = [
		"Site Details" => [
			[["Application Name", "text", "application_name", $w->request("application_name", "Cmfive")]],
			[
				["Company Name", "text", "company_name", $w->request("company_name")],
				["Company URL", "text", "company_url", $w->request("company_url", "http://cmfive.com")]
			],
			[["Company Support Email", "email", "company_support_email", $w->request("company_support_email")]],
			[["Timezone", "select", "timezone", $w->request("timezone", date_default_timezone_get()), $timezones]]
		],
		"Email Server Details" => [
			[
				["Email Layer", "select", "email_layer", $w->request("email_layer", "stmp"), [["stmp", "STMP"], ["sendmail", "Sendmail"]]],
				["Email Command (Sendmail Only)", "text", "sendmail_command", $w->request("sendmail_command", "")]
			],
			[
				["Host", "text", "email_host", $w->request("email_host", "")],
				["Port", "text", "email_port", $w->request("email_port", "")],
				["Use Auth", "checkbox", "email_use_auth", $w->request("email_use_auth", 1)]
			],
			[
				["Username", "text", "email_username", $w->request("email_username", "")],
				["Password", "password", "email_password", $w->request("email_password", "")]
			]
		]
	];
	
	$w->out(Html::multiColForm($form_details, "/install-steps/details"));
}

function details_POST(Web $w) {
	$_POST['email_use_auth'] = (bool) !empty($_POST['email_use_auth']);
	
	// Prefil email data
	$_POST["db_host"] = "{{ db_host }}";
	$_POST["db_username"] = "{{ db_username }}";
	$_POST["db_password"] = "{{ db_password }}";
	$_POST["db_driver"] = "{{ db_driver }}";
	$_POST["db_database"] = "{{ db_database }}";
	
	$_POST['email_port'] = !empty($_POST['email_port']) ? $_POST['email_port'] : 0;
	
	// Render data in config
	InstallService::saveConfigData($_POST);
	
	$w->msg("Details Saved", "/install-steps/database");
}
