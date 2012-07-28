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
 * @version     0.1
 */

// ------------------------------------------------------------------------
class Crystal_Helper_Postgres
{
	
	
	/** ACCEPTS ONLY STRING **/
	static function sanitize_string($string)
	{
		
		if(is_string($string))
		{
			
			return pg_escape_string($string); 			
		}
		elseif(is_numeric($string) or $string == FALSE)
		{
			return $string;		
		}
		else
		{	
			throw new Crystal_Helper_Exception("Helper accepts only strings for add_apostrophe function");
		}

        
    }


    static function add_single_quote($string)
	{
		
		if(is_string($string))
		{	
			return " '" . pg_escape_string($string) . "' ";
		}
		elseif(is_numeric($string) or $string == FALSE)
		{
			
			return $string;
			
		}
		else
		{	
			throw new Crystal_Helper_Exception("Helper accepts only strings for add_single_quote function");
		}
        
    }
	

		
	
	static function add_double_quote($string)
	{
		if(is_string($string))
		{
			return '"' . pg_escape_string($string) . '"';
		}
		elseif(is_numeric($string) or $string == FALSE)
		{
			
			return $string;
			
		}
		else
		{	
			throw new Crystal_Helper_Exception("Helper accepts only strings for add_double_quote function");
		}
        
    }
	
	static function escape_update_values($cols)
	{
		
		

		foreach($cols as $key => $value)
        {

           $updated_cols[] = self::sanitize_string($key)  . "= "  . self::add_single_quote($value)  . " ";


        }

        $temp = implode(',', $updated_cols);


	return $temp;

    }
	
	
	static function escape_update_values_safe($cols)
	{

		foreach($cols as $key => $value)
        {

           $updated_cols[] = self::sanitize_string($key)  . "="  . self::add_single_quoute($value);


        }

        $temp = implode(',', $updated_cols);

	
	    return $temp;

    }
	
	static function clean_db_result($rows)
	{
		
		if(isset($rows) && !empty($rows))
		{
				
		foreach($rows as $key =>  $column)
		{
				
			if(!is_numeric($column))
			{	
				
			$rows[$key]  =  stripslashes($column);
			
			}
		
		}
		
		return $rows;
			
			
		}
	
		
		
		
		
	}
	
		


}