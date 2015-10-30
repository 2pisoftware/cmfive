<?php

class InstallService extends DbService {
	
	/**
	 * Will write data to the twig config template where variables match
	 * 
	 * @param <Array> $data
	 * @return int or FALSE
	 */
	public static function saveConfigData($data) {
		$template_path = "system/modules/install/assets/config.php";
		require_once 'Twig-1.13.2/lib/Twig/Autoloader.php';
		Twig_Autoloader::register();

		$template = null;
		if (file_exists($template_path)) {
			$dir = dirname($template_path);
			$loader = new Twig_Loader_Filesystem($dir);
			$template = str_replace($dir . DIRECTORY_SEPARATOR, "", $template_path);
		} else {
			$loader = new Twig_Loader_String();
			$template = $template_path;
		}
		
		// Render data in config
		$twig = new Twig_Environment($loader, array('debug' => true));
		$twig->addExtension(new Twig_Extension_Debug());

		$config_template = $twig->loadTemplate($template);
		$result_config = $config_template->render($data);
		
		return file_put_contents($template_path, $result_config);
	}
	
	/**
	 * Will put the final config in the project
	 * 
	 * @return null
	 */
	public static function writeConfigToProject() {
		copy("system/modules/install/assets/config.php", "config.php");
		file_put_contents("config.php", "<?php\n\n" . file_get_contents("config.php"));
	}
	
	public static function resetConfigFile() {
		
	}
	
	
	
	
	
	/*********************************************************
	 * Write config.php from a template and variables
	 ********************************************************/
	public static function writeConfig($config) {
		$template_path = "system/modules/install/templates/config.install.tpl.php";
		ob_start();
		require($template_path);
		$result_config=ob_get_contents();
		ob_end_clean();
		@unlink('cache\config.cache');
		return file_put_contents("config.php", "<?php\n\n" .$result_config);
	}
	
	/*********************************************************
	 * Get a PDO connection
	 ********************************************************/	
	 public static function getConnection($port,$driver,$hostname,$username,$password,$database) {
		$pdo;
		// Try and connect
		try {
			$pdo = new DbPDO([
				'port' => $port, 
				'driver' => $driver, 
				'hostname' => $hostname,
				'username' => $username,
				'password' => $password,
				'database' => $database]);
		} catch (Exception $ex) {
			// Return error
			echo ucfirst($driver) . ' returned an error: ' . $ex->getMessage();
			return;
		}
		return $pdo;
	}
	
	/*********************************************************
	 * Execute sql install files scattered through cmFive
	 ********************************************************/
	public static function runInstallSql($pdo) {
		$errors=[];
		$output=[];
		
		$output[]="Clearing main database<br/><hr/>";
		
		// Try and import data
		foreach($pdo->query("SHOW TABLES;") as $row) {
			$pdo->exec("DROP TABLE {$row[0]};");
		}
		
		$output[]="Installing main database SQL<br/><hr/>";
		
		// Run install SQL
		$pdo->exec(file_get_contents('system/install/db.sql'));
		
		$output[]="Installing updates<br/><hr/>";
		
		// Run updates
		foreach(glob('system/install/updates/*.sql') as $file) {
			try {
				$pdo->exec(file_get_contents($file));
			} catch (Exception $e) {
				$errors[]="Error from system update: " . $e->getMessage() . ' in ' . $file;
			}
		}
		
		$output[]="Creating admin user<br/><hr/>";
			// @TODO: Install admin user 
		
		$pdo->exec(file_get_contents('system/install/dbseed.sql'));
		
		// Install system modules
		foreach(glob('system/modules/*', GLOB_ONLYDIR) as $directory) {
			$output[]="Installing " . $directory . " module<br/><hr/>";
			
			// Install system module SQL
			if (file_exists($directory . "/install/db.sql")) {
				try {
					$pdo->exec(file_get_contents($directory . "/install/db.sql"));
				} catch (Exception $e) {
					$errors[]="Error from module:{$directory} install:" . $e->getMessage() . ' in ' . $directory . '/db.sql';
				}
			} else {
				continue;
			}
			
			if (is_dir($directory . "/install/updates")) {
				$output[]="Installing " . $directory . " module updates<br/><hr/>";
		
				// Install system module updates
				foreach(glob($directory . "/install/updates/*.sql") as $module_file) {
					try {
						$pdo->exec(file_get_contents($module_file));
					} catch (Exception $e) {
						$errors[]=$e->getMessage() . ' in ' . $module_file;
					}
				}
			}
		}
		
		// Install individual modules
		foreach(glob('modules/*', GLOB_ONLYDIR) as $directory) {
			$output[]="Installing " . $directory . " module<br/><hr/>";
			
			// Run project modules install SQL
			if (file_exists($directory . "/install/db.sql")) {
				
				try {
					$pdo->exec(file_get_contents($directory . "/install/db.sql"));
				} catch (Exception $e) {
					$errors[]="Error from module install:".$e->getMessage() . ' in ' . $directory;
				}
			} else {
				continue;
			}
		
			// Install project module updates
			if (is_dir($directory . "/install/updates")) {
				$output[]="Installing " . $directory . " module updates<br/><hr/>";
				
				foreach(glob($directory . "/install/updates/*.sql") as $module_file) {
					try {
						$pdo->exec(file_get_contents($module_file));
					} catch (Exception $e) {
						$errors[]="Error from module updates import:<br/>".$e->getMessage() . ' in ' . $module_file;
					}
				}
			}
		}
		return array('errors'=>$errors,'output'=>$output);
	}

	/*********************************************************
	 * Create an admin user
	 ********************************************************/
	public static function createAdminUser($pdo,$adminUsername,$adminPassword,$adminFirstName,$adminLastName,$adminEmail) {
		$errors=[];
		$output=[];
		try {
			$statement = $pdo->prepare("INSERT INTO contact (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES (NULL, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ?, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0', NULL, NULL);");
			$statement->bindParam(1, $adminFirstName);
			$statement->bindParam(2, $adminLastName);
			$statement->bindParam(3, $adminEmail);
			$result = $statement->execute();

			$contact_id = $pdo->lastInsertId();

			$user_statement = $pdo->prepare("INSERT INTO user (`id`, `login`, `password`, `password_salt`, `contact_id`, `password_reset_token`, `dt_password_reset_at`, `redirect_url`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES (NULL, ?, ?, ?, ?, NULL, NULL, 'main/index', '1', '1', '0', '0', CURRENT_TIMESTAMP, NULL);");
			$user_statement->bindParam(1, $adminUsername);

			// Generate encrypted password
			$salt = User::generateSalt();
			$output[]="Salt: " . $salt;
			$output[]="Password: " . $adminPassword;
			$password = sha1($salt . trim($adminPassword));
			$output[]="Encrypted Password: " . $password;
			
			$user_statement->bindParam(2, $password);
			$user_statement->bindParam(3, $salt);
			$user_statement->bindParam(4, $contact_id);
			$result = $user_statement->execute();

			$user_id = $pdo->lastInsertId();
			$role_statement = $pdo->prepare("INSERT INTO user_role (`id`, `user_id`, `role`) VALUES (NULL, ?, 'user');");
			$role_statement->bindParam(1, $user_id);
			$result = $role_statement->execute();
			
			$output[]="Admin user created";
			
		} catch (Exception $e) {
			$errors[]="Failed to create user: " . $e->getMessage();
		}
		return array('errors'=>$errors,'output'=>$output);
	}
	
}
