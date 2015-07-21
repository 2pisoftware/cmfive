<?php
function index_ALL(Web $w) {
	$p = $w->pathMatch("install", "step", "step_number");
	
	// Redirect to install of no actions given
	if(empty($p['install'])) {
		$w->redirect('install/step/1');
	}
	
	// Set current step
	$step = !empty($p['step_number']) ? intval($p['step_number']) : 1;
	
	// Redirect to first step if no POST data for step 2/3/4
	if ($step > 1 && $_SERVER["REQUEST_METHOD"] != "POST") {
		$w->redirect('install/step/1');
	}
	$w->ctx("step", $step);
	
	// Form action is the next step (@TODO: What happens with errors?)
	$form_action = "/install/step/" . ($step+1);
	
	// Build database details form
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
		]
	];

	$w->ctx("form_details", Html::multiColForm($form_details, $w->localUrl("/install/step/2"), "POST", "Check Connection", "install_form", null, null, "_self", true, 
		["db_driver" => ["required"], "db_username" => ["required"], "db_password" => ["required"], "db_port" => ["required"], "db_hostname" => ["required"]]));
	
	// Decide what piece of functionality to dispaly
	switch($step) {
		case 2: {
			// Attempt to connect to the database
			$port = isset($_POST['db_port']) && !empty($_POST['db_port']) ? ";port=".$_POST['db_port'] : "";
			$url = "{$_POST['db_driver']}:host={$_POST['db_hostname']}{$port}";
			try {
				$pdo = new PDO($url, $_POST['db_username'], $_POST['db_password']);
			} catch(PDOException $e) {
				$w->ctx('error', "Couldn't connect to the database!<br />".$e->getMessage());
				return;
			}
			$w->ctx('info', 'Successfully connected to database');
			
			// Get list fo databases
			$sql = 'SHOW databases;';
			$databases = array();
			foreach($pdo->query($sql) as $row) {
				if($row['0'] != 'information_schema') {
					$hasTables = !empty($pdo->exec("USE {$row[0]}; SHOW TABLES;"));
					$databases[] = array($row[0] . ($hasTables ? " DATABASE IS NOT EMPTY" : ""), $row[0]);
				}
			}

			$form_database = [
				"Select a database" => [
					[["", "select", "selected_database", null, $databases]]
				]
			];
			
			$w->ctx("form_database", Html::multiColForm($form_database, $w->localUrl("/install/step/3"), "POST", "Import required tables", "install_form", null, null, "_self", true, ["selected_database" => ["required"]]));
			break;
		}
		case 3: {
			$port = isset($_POST['db_port']) && !empty($_POST['db_port']) ? ";port=".$_POST['db_port'] : "";
			$url = "{$_POST['db_driver']}:dbname={$_POST['selected_database']};host={$_POST['db_hostname']}{$port}";
			try {
				$pdo = new PDO($url, $_POST['db_username'], $_POST['db_password']);
			} catch(PDOException $e) {
				$w->ctx('error', "Couldn't connect to the database!<br />".$e->getMessage());
				return;
			}
			
			// Clear database and import all SQL scripts
			$sql = 'SHOW TABLES;';
			foreach($pdo->query($sql) as $row) {
				$pdo->exec("DROP TABLE {$row[0]};");
			}
			$pdo->exec(file_get_contents('system/install/db.sql'));
			foreach(glob('system/install/updates/*.sql') as $file) {
				$pdo->exec(file_get_contents($file));
			}
			$pdo->exec(file_get_contents('system/install/dbseed.sql'));
			$pdo->exec(file_get_contents('system/install/userscontactsroles.sql'));
			$w->ctx('info', 'Database tables successfully imported');
			
			// Build the config form
//			$config_form = [
//				"Application Information" => [
//					[["Application Name", ]]
//				]
//			];
//			
			break;
		}
		case 4: {
			$tpl = new WebTemplate();
			$tpl->set_vars($_POST);
			$config = "<?php\n";
			$config .= $tpl->fetch('system/modules/install/templates/config.tpl.php');
			file_put_contents('config.php', $config);
			break;
		}
		// First screen is default
		case 1:
		default: {
			
		}
	}
}
