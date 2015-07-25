<?php
/**
 * Class that compares 
 * 
 * @author Robert Lockerbie
 */

class DbDiff {
	private $searchPaths = array(
		'/system/modules/*',
		'/modules/*'
	);
	private $dbpdo;
	private $web;
	public function __construct(DbPdo $pdo, Web $w) {
		$this->dbpdo = $pdo;
		$this->web = $w;
	}
	/*
	 * Attempts to automatically fix a missing table by trying to find and load the install sql for that table
	 */
	public function getRepair($table_name) {
		$this->web->Log->setLogger('DB_REPAIR')->info("Table $table_name does not exist in the database, attempting repair.");
		$repaired = false;
		foreach($this->searchPaths as $path) {
			foreach(glob(ROOT_PATH . $path, GLOB_ONLYDIR) as $moduleDirectory) {
				if(is_dir($moduleDirectory . '/install')) {
					foreach(glob($moduleDirectory . '/install/*.sql') as $sqlFile) {
						$sql = file_get_contents($sqlFile);
						if(false !== strpos($sql, $table_name)) {
							$this->web->Log->setLogger('DB_REPAIR')->info("Found table create statement in $sqlFile, attempting to load.");
							$sql = preg_replace('%^\-\-.*$%m', '', $sql);
							$sql = str_replace("\n", '', $sql);
							//Only run the sql create for the missing table...
							if(preg_match('%CREATE TABLE( IF NOT EXISTS|) (|`)'.$table_name.'.*;%Uis', $sql, $matches)) {
								try {
									$this->dbpdo->sql($matches[0]);
								} catch(Exception $e) {
									$this->web->Log->setLogger('DB_REPAIR')->error("Couldn't create table ".$e->getMessage());
									break(3);
								}
								$this->dbpdo->updateTableList();
								//Assume success...
								$repaired = true;
								break(3);
							} else {
								$this->web->Log->setLogger('DB_REPAIR')->error("Couldn't find create table statement.");
								break(3);
							}
						}
					}
				}
			}
		}
		if($repaired) {
			$this->web->Log->setLogger('DB_REPAIR')->info("Table $table_name successfully repaired.");
			return $this->dbpdo->get($table_name);
		} else {
			$this->web->Log->setLogger('DB_REPAIR')->error("Table $table_name couldn't be repaired.");
			return NULL;
		}
	}
	
}