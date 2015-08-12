<?php

function index_ALL(Web $w) {
	echo "hello";
//	$p = $w->pathMatch("install", "step", "step_number");
//	
//	// Redirect to install of no actions given
//	if(empty($p['install'])) {
//		$w->redirect('install/index/1');
//	}
//	
//	// Set current step
//	$step = !empty($p['step_number']) ? intval($p['step_number']) : 1;
//	
//	// Redirect to first step if no POST data for step 2/3/4
//	if ($step > 1 && $_SERVER["REQUEST_METHOD"] != "POST") {
//		$w->redirect('install/index/1');
//	}
//	$w->ctx("step", $step);
//	$w->ctx("form_action", "/install/index/"+($step+1));
//	$drivers = '';
//	foreach(PDO::getAvailableDrivers() as $driver) {
//		$drivers .= "<option>$driver</option>";
//	}
//	$w->ctx('step_description', 'Database details');
//	if('' == $drivers) {
//		$w->ctx('error', "No database drivers available!");
//		return;
//	}
//	$w->ctx('driver_options', $drivers);
//	if($step == 2) {
//		if(!empty($_POST)) {
//			if(empty($_POST['db_username'])) {
//				$w->ctx('error', "Database username cannot be empty");
//			} else if(empty($_POST['db_hostname'])) {
//				$w->ctx('error', "Database hostname cannot be empty");
//			}
//			$port = isset($_POST['db_port']) && !empty($_POST['db_port']) ? ";port=".$_POST['db_port'] : "";
//			$_SESSION['install_db_port'] = $port;
//			$_SESSION['install_db_driver'] = $_POST['db_driver'];
//			$_SESSION['install_db_hostname'] = $_POST['db_hostname'];
//			$_SESSION['install_db_username'] = $_POST['db_username'];
//			$_SESSION['install_db_password'] = $_POST['db_password'];
//		}
//		$url = "{$_SESSION['install_db_driver']}:host={$_SESSION['install_db_hostname']}{$_SESSION['install_db_port']}";
//		try {
//			$pdo = new PDO($url, $_SESSION['install_db_username'], $_SESSION['install_db_password']);
//		} catch(PDOException $e) {
//			$w->ctx('error', "Couldn't connect to the database!");
//			$w->ctx('step', 1);
//			$w->ctx("form_action", "/install/index/2");
//			return;
//		}
//		$w->ctx('info', 'Successfully connected to database');
//		$sql = 'SHOW databases;';
//		$databases = array();
//		foreach($pdo->query($sql) as $row) {
//			if(!in_array($row['0'], array('mysql', 'information_schema', 'performance_schema'))) {
//				$databases[$row[0]] = array();
//			}
//		}
//		foreach($databases as $database=>$tables) {
//			$pdo->exec("USE $database;");
//			foreach($pdo->query('SHOW TABLES;') as $row) {
//				$databases[$database][] = $row[0];
//			}
//		}
//		$w->ctx('step_description', 'Database selection');
//		$w->ctx('databases', $databases);
//	} else if($step == 3) {
//		if(!empty($_POST)) {
//			$_SESSION['install_db_database'] = $_POST['db_database'];
//		}
//		try {
//			$url = "{$_SESSION['install_db_driver']}:dbname={$_SESSION['install_db_database']};host={$_SESSION['install_db_hostname']}{$_SESSION['install_db_port']}";
//			$pdo = new PDO($url, $_SESSION['install_db_username'], $_SESSION['install_db_password']);
//		} catch(PDOException $e) {
//			$w->ctx('error', "Couldn't connect to the database!");
//			return;
//		}
//		//Make sure database is empty...
//		$sql = 'SHOW TABLES;';
//		foreach($pdo->query($sql) as $row) {
//			$pdo->exec("DROP TABLE {$row[0]};");
//		}
//		$pdo->exec(file_get_contents('system/install/db.sql'));
//		foreach(glob('system/install/updates/*.sql') as $file) {
//			$pdo->exec(file_get_contents($file));
//		}
//		$pdo->exec(file_get_contents('system/install/dbseed.sql'));
//		//$pdo->exec(file_get_contents('system/install/userscontactsroles.sql'));
//		$w->ctx('info', 'Database tables successfully imported');
//		$w->ctx('step_description', 'Configuration options');
//	} else if($step == 4) {
//		$w->ctx('step_description', 'Install complete');
//		try {
//			$url = "{$_SESSION['install_db_driver']}:dbname={$_SESSION['install_db_database']};host={$_SESSION['install_db_hostname']}{$_SESSION['install_db_port']}";
//			$pdo = new PDO($url, $_SESSION['install_db_username'], $_SESSION['install_db_password']);
//		} catch(PDOException $e) {
//			$w->ctx('error', "Couldn't connect to the database!");
//			return;
//		}
//		//Add admin user
//		$statement = $pdo->prepare('INSERT INTO contact(firstname,lastname,email) VALUES(:firstname,:lastname,:email)');
//		$statement->execute(array(
//			':firstname' => $_POST['firstname'],
//			':lastname' => $_POST['lastname'],
//			':email' => $_POST['email'],
//		));
//		$statement->closeCursor();
//		$contactId = $pdo->lastInsertId();
//		$statement = $pdo->prepare('INSERT INTO user(login,password,password_salt,contact_id,is_admin,is_active) VALUES(:login,:password,:password_salt,:contact_id,:is_admin,:is_active)');
//		$salt = md5(uniqid(rand(), TRUE));
//		$password = sha1($salt . $_POST['admin_password']);
//		$statement->execute(array(
//			':login' => $_POST['admin_user'],
//			':password' => $password,
//			':password_salt' => $salt,
//			':contact_id' => $contactId,
//			':is_admin' => 1,
//			':is_active' => 1
//		));
//		$tpl = new WebTemplate();
//		$vars = array(
//			'application_name' => $_POST['app_name'],
//			'company_name' => $_POST['company_name'],
//			'company_url' => $_POST['company_url'],
//			'timezone' => $_POST['timezone'],
//			'db_hostname' => $_SESSION['install_db_hostname'],
//			'db_username' => $_SESSION['install_db_username'],
//			'db_password' => $_SESSION['install_db_password'],
//			'db_database' => $_SESSION['install_db_database'],
//			'db_driver' => $_SESSION['install_db_driver'],
//			'email_layer' => $_POST['email_layer'],
//			'email_host' => $_POST['email_host'],
//			'email_auth' => isset($_POST['email_auth']),
//			'email_username' => $_POST['email_username'],
//			'email_password' => $_POST['email_password'],
//			'rest_api_key' => $_POST['rest_api_key'],
//			'checkCSRF' => isset($_POST['checkCSRF']),
//			'allow_from_ip' => array_values($_POST['allow_from_ip']),
//		);
//		$tpl->set_vars($vars);
//		$config = "<?php\n";
//		$config .= $tpl->fetch('system/modules/install/templates/config.tpl.php');
//		file_put_contents('config.php', $config);
//		$w->ctx('info', 'Install complete');
//	}
}
