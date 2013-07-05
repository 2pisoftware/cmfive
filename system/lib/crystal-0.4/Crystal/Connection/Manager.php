<?php
/**
 * Crystal DBAL
 *
 * An open source application for database manipulation
 *
 * @package		Crystal DBAL
 * @author		Martin Rusev
 * @link		http://crystal.martinrusev.net
 * @since		Version 0.1
 * @version     0.3
 */

// ------------------------------------------------------------------------

class Crystal_Connection_Manager
{
    

    function __construct($connection = null, $config_params = null)
    {
	
		/** GETS PREFERED CONNECTION DETAILS **/
        if(isset($connection))
        {
            /** DOESN'T NEED Config Reader if configuration is array,
             *  the fastest option for Crystal
             ***/
        	if(is_array($connection))
        	{
        		$db_config = $connection;
        	}
        	else
        	{
        		$db_config = Crystal_Config_Reader::get_db_config($connection, $config_params);	
        	}
        	
        }
        /** FALLS BACK TO DEFAULT **/
        else
        {
        	
           $db_config = Crystal_Config_Reader::get_db_config('default');
        }

     

        if ($db_config == false )
        {
            throw new Crystal_Connection_Exception("Invalid configuration file.");
        }
        elseif(!isset($db_config['driver']) && empty($db_config['driver']))
        {

             throw new Crystal_Connection_Exception("Invalid database adapter");
            
        }


        /** CHECKS DATABASE DRIVER **/
        switch ($db_config['driver'])
        {
            case 'mysql':
            $this->conn = new Crystal_Connection_Adapter_Mysql($db_config);
            break;
			
			
			case 'postgres':
            $this->conn = new Crystal_Connection_Adapter_Postgres($db_config);
            break;
			
			
			
            default:
            throw new Crystal_Connection_Exception("Invalid database adapter");
            break;
        }



        return $this->conn;


    }


    


    

    
}

