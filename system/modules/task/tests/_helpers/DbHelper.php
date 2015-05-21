<?php
/**
 * Helper to run custom sql files from tests
 * Code taken from https://github.com/ianckc/Codeception-DbHelper/blob/master/tests/_helpers/DbHelper.php
 */
namespace Codeception\Module;

// here you can define custom functions for WebGuy 

class DbHelper extends \Codeception\Module
{
  
    /**
     * Run all sql files in a given directory
     */
    public function runSQLQueries($dir = null)
    {
        if(!is_null($dir)){
            
            /**
             * We need the Db module to run the queries
             */
            $dbh = $this->getModule('Db');
            
            /**
             * The Db driver load function requires an array
             */
            $queries = array();

            /**
             * Get all the queries in the directory
             */
            foreach(glob('tests'.DIRECTORY_SEPARATOR.'_data'.DIRECTORY_SEPARATOR. $dir.DIRECTORY_SEPARATOR.'*.sql') as $sqlFile){
                $query = file_get_contents($sqlFile);
                echo $query;
                array_push($queries, $query);
            }
            /**
             * If there are queries load them
             */
            if(count($queries) > 0){
                $dbh->driver->load($queries);
            }
            
        }
    }
    public function runSQL($sql) {
		if(!empty($sql) > 0){
			$dbh = $this->getModule('Db');
			$dbh->driver->load($sql);
		}
	}
	/*
	 * Run SQL file relative to tests/_data
	*/
	public function runSQLFile($sqlFile) {
		$sql = file_get_contents('tests'.DIRECTORY_SEPARATOR.'_data'.DIRECTORY_SEPARATOR.$sqlFile);
        if(!empty($sql) > 0){
			$dbh = $this->getModule('Db');
			$dbh->driver->load($sql);
		}
	}
	
  
}
