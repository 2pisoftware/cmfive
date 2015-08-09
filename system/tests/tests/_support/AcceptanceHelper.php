<?php

namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
define('DS', DIRECTORY_SEPARATOR); 

class AcceptanceHelper extends \Codeception\Module
{
  /**
     * Run all sql files in a given directory
     */
    public function runSQLQueries($dir = null)
    {
		if(!is_null($dir)){
             // parse parameters to extract path (not symlinked source path)
			 $env='';
			 $basePath='';
			 for($i=0; $i< count($GLOBALS['argv']); $i++) {
				 if (strpos(' '.$GLOBALS['argv'][$i],'--config=')>0) {
					$pathParts=explode('config=',$GLOBALS['argv'][$i]);
					$basePath=$pathParts[1].DS;
				 } else if (strpos(' '.$GLOBALS['argv'][$i],'--env')>0) {
					 $env=trim($GLOBALS['argv'][$i+1]);
				 }
			 }
			 
			//include_once($basePath.'tests'.DS.'Spyc.php');
			$codeceptionConfig = \Spyc::YAMLLoad($basePath.'/codeception.yml');
			
			$dbUser='';
			$dbPass='';
			$dbName='';
			if (strlen($env)>0) {
				$dbUser=$codeceptionConfig['env'][$env]['modules']['config']['Db']['user'];
				$dbPass=$codeceptionConfig['env'][$env]['modules']['config']['Db']['password'];
				$dbName=explode("dbname=",$codeceptionConfig['env'][$env]['modules']['config']['Db']['dsn'])[1];
			} else {
				$dbUser=$codeceptionConfig['modules']['config']['Db']['user'];
				$dbPass=$codeceptionConfig['modules']['config']['Db']['password'];
				$dbName=explode("dbname=",$codeceptionConfig['modules']['config']['Db']['dsn'])[1];
			}
			$sql='';
			$mysqli = new \mysqli("localhost", $dbUser, $dbPass, $dbName);
			foreach (glob($basePath.'tests'.DS.'_data'.DS. $dir.DS.'*.sql') as $path) {
				if (file_exists($path)) {
					$output=array();
					$sql=file_get_contents($path);
					echo "load sql ".$path;
					$result = mysqli_multi_query($mysqli,$sql); //implode('\n',$sql));
					// flush multi_queries
					while ($mysqli->more_results() && $ir=$mysqli->next_result()) {if (!$ir) echo $mysqli->error;} 
				}
			}			 
            
			ob_flush();
            
        }
    }
}
