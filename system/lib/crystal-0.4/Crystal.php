<?php
/**
 *  Crystal 
 *
 * An open source application for database manipulation
 *
 * @package		Crystal 
 * @author		Martin Rusev
 * @link		http://crystal-project.net
 * @since		Version 0.1
 * 
 * 
 */

// ------------------------------------------------------------------------

/* DEFINE APPLICATION GLOBAL CONSTANTS **/
define('CRYSTAL_DS',  DIRECTORY_SEPARATOR);
define('CRYSTAL_BASE', dirname(__FILE__));
define('CRYSTAL_BASEPATH', CRYSTAL_BASE . CRYSTAL_DS . 'Crystal' . CRYSTAL_DS);
define('CRYSTAL_CONFIG', CRYSTAL_BASE . CRYSTAL_DS . 'config' . CRYSTAL_DS . 'database.php');

class Crystal
{
	
	const VERSION = '0.4';

    function __construct()
    {
        echo "Crystal is static class, no instances allowed";
        exit;
    }

        
      
    static public function db($connection = null, $additional_parameters = null)
    {
    	return Crystal_Query_Common::db($connection, $additional_parameters);
        
    }     
   
    static public function manipulation($connection = null)
    {
    	
        return Crystal_Manipulation_Common::db($connection);
        
    }


    static public function validation_legacy($rules, $data, $db=null)
    {

        return new Crystal_Validator($rules, $data, $db);

        
    }
    
    
    /** Validation class with new syntax - Crystal 0.4 and beyond **/
    static public function validation($rules, $data, $db=null)
    {
    	
    	return new Crystal_ValidatorNew($rules, $data, $db);
    	
    }
	
	
	public static function crystal_autoload($class_name)
	{

		/** LOADS CRYSTAL SPECIFIC CLASSESS ONLY **/
		$pattern = '/^Crystal/i';
		$match = preg_match($pattern, $class_name, $matches);
		
		if($match != 0 && $match != False)
		{
			$path = str_replace("_", CRYSTAL_DS, $class_name);
	        
	        if(file_exists(CRYSTAL_BASE . CRYSTAL_DS . $path . '.php'))
	        {
	            include(CRYSTAL_BASE . CRYSTAL_DS . $path . '.php');
	        }
	        else
	        {
	           throw new Exception("Cannot find requested class " . $class_name . " in " . $path);
	        }
	        
	
	        if(!class_exists($class_name))
	        {
	            throw new Exception("Invalid Class name ". $class_name);
	        }
		}
	
	}
	
	

}
spl_autoload_register(array('Crystal', 'crystal_autoload'));
