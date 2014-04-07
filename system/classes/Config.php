<?php

/**
 * This class is responsible for storing and accessing the configurations for each class. 
 *
 * @author Adam Buckley
 */

class Config {
    
    // Storage array
    private static $register = array();
    
    /**
     * This function will set a key in an array
     * to the value given
     *
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public static set($key, $value) {
        
    }
    
    /**
     * This function will attempt to return a
     * key out of the array
     *
     * @param string $key
     * @return Mixed the value
     */
    public static get($key) {
        $exploded_key = explode('.', $key);
    }
}
    