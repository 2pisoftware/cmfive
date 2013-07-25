<?php
/**
 * Crystal 
 *
 * An open source application for database manipulation
 *
 * @package		Crystal DBAL
 * @author		Martin Rusev
 * @link		http://crystal-project.net
 * @since		Version 0.1
 * @version     0.4
 */

// ------------------------------------------------------------------------
class Crystal_Connection_Adapter_Mysql
{


    function __construct($database_config, $params=null)
    {
			
			
			/** CHECKS FOR PORT **/
			$port = (isset($database_config['port'])?$database_config['port']:'3306');
			$hostname = (isset($database_config['hostname'])?$database_config['hostname']:'localhost');
			$charset = (isset($database_config['char_set'])?$database_config['char_set']:'utf8');
			
            $this->db = mysqli_connect($hostname, $database_config['username'], $database_config['password'],$database_config['database'],$port);
             /** SETS DATABASE COLLATION **/
             $this->_set_charset($charset);
          

            if (!$this->db)
            {
               throw new Crystal_Connection_Exception("Cannot connect to database");
            }
			  

            return $this->db;


    }


    private function _set_charset($charset = null)
    {
    	
    	if(isset($charset) && !empty($charset))
    	{
    		 mysqli_query($this->db,"SET NAMES " . $charset);
    	}
    	else
    	{
    		mysqli_query($this->db,"SET NAMES utf8");
    	}
       
    }


    
}