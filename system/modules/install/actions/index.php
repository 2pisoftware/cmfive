<?php
function index_ALL(Web $w) {
	$p = $w->pathMatch("install", "step", "step_number");
	if(empty($p['install'])) $w->redirect('install/step/1');
	$step = $p['step_number'];
	$w->ctx("step", $step);
	$w->ctx("form_action", "/install/step/"+($step+1));
	if($step == 2) {
		if(!empty($_POST)) {
			$config = &$_POST;
			$port = isset($config['db_port']) && !empty($config['db_port']) ? ";port=".$config['db_port'] : "";
			$url = "{$config['db_driver']}:host={$config['db_hostname']}{$port}";
			try {
				$pdo = new PDO($url, $config['db_username'], $config['db_password']);
			} catch(PDOException $e) {
				$w->ctx('error', "Couldn't connect to the database!<br />".$e->getMessage());
				return;
			}
			$w->ctx('info', 'Successfully connected to database');
			$sql = 'SHOW databases;';
			$databases = array();
			foreach($pdo->query($sql) as $row) {
				if($row['0'] != 'information_schema') {
					$databases[$row[0]] = array();
				}
			}
			foreach($databases as $database=>$tables) {
				$pdo->exec("USE $database;");
				foreach($pdo->query('SHOW TABLES;') as $row) {
					$databases[$database][] = $row[0];
				}
			}
			$w->ctx('databases', $databases);
		}
	} else if($step == 3) {
		if(!empty($_POST)) {
			$config = &$_POST;
			$port = isset($config['db_port']) && !empty($config['db_port']) ? ";port=".$config['db_port'] : "";
			$url = "{$config['db_driver']}:dbname={$config['db_database']};host={$config['db_hostname']}{$port}";
			try {
				$pdo = new PDO($url, $config['db_username'], $config['db_password']);
			} catch(PDOException $e) {
				$w->ctx('error', "Couldn't connect to the database!<br />".$e->getMessage());
				return;
			}
			//Make sure database is empty...
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
		}
	} else if($step == 4) {
		$tpl = new WebTemplate();
		$tpl->set_vars($_POST);
		$config = "<?php\n";
		$config .= $tpl->fetch('system/modules/install/templates/config.tpl.php');
		file_put_contents('config.php', $config);
	}
}
