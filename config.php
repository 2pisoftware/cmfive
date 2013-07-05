<?php

//========== Application Modules Configuration ===============

$modules['main'] = array(
		'active' => true,
		'path' => 'modules',
		'topmenu' => false,
		'application_name' => 'cmfive',
		'company_name' => 'cmfive',
		'company_url' => 'http://github.com/careck/cmfive',
);

//=============== Timezone ==================================

date_default_timezone_set('Australia/Sydney');

//========== Database Configuration ==========================

$MYSQL_DB_HOST = 'localhost';
$MYSQL_USERNAME = 'root';
$MYSQL_PASSWORD = '';
$MYSQL_DB_NAME = 'cmfive';

//========= Application Log Level ===========================

$LOG_LEVEL = 'debug';
