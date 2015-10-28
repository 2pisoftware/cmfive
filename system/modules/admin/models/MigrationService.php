<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

class MigrationService extends DbService {
	
	public function getAvailableMigrations() {
		$availableMigrations = [];
		
		// Read all modules directories for any migrations that need to run
		foreach($this->w->modules() as $module) {
			// Check modules folder
			$module_path = 'modules' . DS . $module . DS . 'install' . DS . 'migrations';
			$system_module_path = 'system' . DS . 'modules' . DS . $module . DS . 'install' . DS . 'migrations';
			
			$migration_paths = [$module_path, $system_module_path];
			if (empty($availableMigrations[$module])) {
				$availableMigrations[$module] = [];
			}
			
			foreach($migration_paths as $migration_path) {
				if (is_dir(ROOT_PATH . DS . $migration_path)) {
					foreach(scandir(ROOT_PATH . DS . $migration_path) as $file) {
						if (!is_dir($file)) {
							$classname = explode('.', str_replace('-', '.', $file));
							if (!empty($classname[1])) {
								$availableMigrations[$module][$migration_path . DS . $file] = $classname[1];
							} else {
								$this->w->Log->error("Migration '" . $file . "' does not conform to naming convention");
							}
						}
					}
				}
			}
		}
		
		return $availableMigrations;
	}
	
	public function getMigrationByClassname($classname) {
		return $this->getObject('Migration', ['classname' => $classname]);
	}
	
	public function isInstalled($classname) {
		return $this->w->db->get('migration')->where('classname', $classname)->count() == 1;
	}
	
	public function getInstalledMigrations() {
		return $this->w->db->get('migration')->fetch_all();
	}
	
	public function runMigrations() {
		$alreadyRunMigrations = $this->getInstalledMigrations();
		$availableMigrations = $this->getAvailableMigrations();
		
		// Sort available into ascending order
		uasort($availableMigrations, function($a, $b) {
			return strcmp(substr($a, strrpos($a, '/') + 1), substr($b, strrpos($b, '/') + 1));
		});
		
		if (empty($availableMigrations)) {
			return;
		}
		
		if (!empty($alreadyRunMigrations)) {
			foreach($alreadyRunMigrations as $alreadyRunMigration) {
				if (array_key_exists($alreadyRunMigration['path'], $availableMigrations)) {
					unset($availableMigrations[$alreadyRunMigration['path']]);
				}
			}
		}

		if (!empty($availableMigrations)) {
			$this->w->db->startTransaction();

			try {
				$mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
					'connection' => $this->w->db,
					'name' => Config::get('database.database'),
	//				'host' => Config::get('database.hostname'),
	//				'port' => Config::get('database.port'),
	//				'user' => Config::get('database.username'),
	//				'pass' => Config::get('database.password'),
				]);

				foreach($availableMigrations as $migration_path => $migration) {

					if (file_exists(ROOT_PATH . '/' . $migration_path)) {
						include_once ROOT_PATH . '/' . $migration_path;

						if (class_exists($migration)) {
							$this->w->Log->setLogger("MIGRATION")->info("Running migration: " . $migration);

							$migration_class = new $migration(1);
							$migration_class->setAdapter($mysql_adapter);
							$migration_class->up();

							// Insert migration record into DB
							$migration_object = new Migration($this->w);
							$migration_object->path = $migration_path;
							$migration_object->classname = $migration;
							$migration_object->insert();

							$this->w->Log->setLogger("MIGRATION")->info("Migration has run");
						}
					}
				}

				$this->w->db->commitTransaction();
				return count($availableMigrations) . ' migration' . (count($availableMigrations) == 1 ? ' has' : 's have') . ' run'; 
			} catch (Exception $e) {
				$this->w->out("Error with a migration: " . $e->getMessage());
				$this->w->Log->setLogger("MIGRATION")->error("Error with a migration: " . $e->getMessage());
				$this->w->db->rollbackTransaction();
			}
		} else {
			return "No migrations to run!";
		}
	}
	
}