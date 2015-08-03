<?php

function import_GET(Web $w) {
	$w->setLayout(null);
   
	$message = ['success' => 0, 'message' => ''];
	
	// Validate the given parameters
	$missing = array_filter($_GET, function($parameter) {
		return empty($parameter);
	});
	
	if (!empty($missing)) {
		output('The following values are required: ' . implode(', ', array_keys($missing)));
		return;
	}
	
	// Try and connect
	try {
		$pdo = new DbPDO([
			'port' => $_GET['db_port'], 
			'driver' => $_GET['db_driver'], 
			'hostname' => $_GET['db_hostname'],
			'username' => $_GET['db_username'],
			'password' => $_GET['db_password'],
			'database' => $_GET['db_database']]);
	} catch (Exception $ex) {
		// Return error
		output(ucfirst($_GET['db_driver']) . ' returned an error: ' . $ex->getMessage());
		return;
	}
	
	// Write config details to file
	InstallService::saveConfigData($_GET);
	
	// Try and import data
	foreach($pdo->query("SHOW TABLES;") as $row) {
		$pdo->exec("DROP TABLE {$row[0]};");
	}
	
	output("Installing main database SQL<br/><hr/>");
	
	// Run install SQL
	$pdo->exec(file_get_contents('system/install/db.sql'));
	
	output("Installing updates<br/><hr/>");
	
	// Run updates
	foreach(glob('system/install/updates/*.sql') as $file) {
		try {
			$pdo->exec(file_get_contents($file));
		} catch (Exception $e) {
			output("Error from system update:");
			output($e->getMessage() . '<br/>in ' . $file);
		}
	}
	
	output("Creating admin user<br/><hr/>");
		// @TODO: Install admin user 
	
	$pdo->exec(file_get_contents('system/install/dbseed.sql'));
	
	// Install system modules
	foreach(glob('system/modules/*', GLOB_ONLYDIR) as $directory) {
		output("Installing " . $directory . " module<br/><hr/>");
		
		// Install system module SQL
		if (file_exists($directory . "/install/db.sql")) {
			try {
				$pdo->exec(file_get_contents($directory . "/install/db.sql"));
			} catch (Exception $e) {
				output("Error from module:{$directory} install:");
				output($e->getMessage() . '<br/>in ' . $directory . '/db.sql');
			}
		} else {
			continue;
		}
		
		if (is_dir($directory . "/install/updates")) {
			output("Installing " . $directory . " module updates<br/><hr/>");
	
			// Install system module updates
			foreach(glob($directory . "/install/updates/*.sql") as $module_file) {
				try {
					$pdo->exec(file_get_contents($module_file));
				} catch (Exception $e) {
					output($e->getMessage() . '<br/>in ' . $module_file);
				}
			}
		}
	}
	
	// Install individual modules
	foreach(glob('modules/*', GLOB_ONLYDIR) as $directory) {
		output("Installing " . $directory . " module<br/><hr/>");
		
		// Run project modules install SQL
		if (file_exists($directory . "/install/db.sql")) {
			
			try {
				$pdo->exec(file_get_contents($directory . "/install/db.sql"));
			} catch (Exception $e) {
				output("Error from module install:");
				output($e->getMessage() . '<br/>in ' . $direcotory);
			}
		} else {
			continue;
		}
	
		// Install project module updates
		if (is_dir($directory . "/install/updates")) {
			output("Installing " . $directory . " module updates<br/><hr/>");
			
			foreach(glob($directory . "/install/updates/*.sql") as $module_file) {
				try {
					$pdo->exec(file_get_contents($module_file));
				} catch (Exception $e) {
					output("Error from module updates import:<br/>");
					output($e->getMessage() . '<br/>in ' . $module_file);
				}
			}
		}
	}

	// Write the config to the project
	InstallService::writeConfigToProject();
	
	output('Import successful');
}

function output($val) {
    echo $val . "<br/>";
}