<?php

/**
 * This class is designed to manage page traversal (history) by storing values in the $_SESSION
 * Calling History::add($name) will add that name and a timestamp to an array in session with the
 * current url path as the key
 * 
 * NOTE: this means that any GET/POST parameters CANNOT be stored along with the path
 * 
 * @author Adam Buckley
 */

class History {
    
    // Storage array
    private static $cookie_key = 'cmfive_history';

    /**
     * This function adds a history value to the SESSION
     * @param String $name
     */
    public static function add($name, Web $w = null) {
        // Sanitise the string
        if (!empty($name)) {
            $name = trim(htmlspecialchars(strip_tags($name)));
        }
        
        $register = array();
        if (!empty($_SESSION[self::$cookie_key])) {
            // Get history form session and sort ($register is by reference)
            $history = &$_SESSION[self::$cookie_key];
            uasort($history, array('History', 'sort'));
        }

        // Prepend module name to current name
        if (!empty($w)) {
            $name = $w->_module . (!empty($name) ? ': ' . $name : '');
        }
        
        // Store array in SESSION
        $history[parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)] = array('name' => $name, 'time' => time());
    }
    
    /**
     * This function will attempt to return a $length amount of elements
     * out of the History array by $key (key optional)
     *
     * @param string $key (optional)
     * @param int $length (optional)
     * @return Array the history
     */
    public static function get($key = NULL, $length = 0) {
        // Load cookie storage into array to be manipulated
        if (empty($_SESSION[self::$cookie_key])) {
            return NULL;
        }
        
        // Get history form cookie and sort
        $history = $_SESSION[self::$cookie_key];
        uasort($history, array('History', 'sort'));
        
        // Return history with empty key
        if (empty($key)) {
            if (0 < $length) {
                // Return last $length elements (http://stackoverflow.com/questions/5468912/php-get-the-last-3-elements-of-an-array)
                return array_slice(self::$register, $length * -1, $length, true);
            }
            return $history;
        }
        
        if (empty($history[$key])) {
            return NULL;
        } else {
            return $history[$key];
        }
    }
    
    /**
     * This is a sort function for a History entry
     * 
     * @param Array $a
     * @param Array $b
     * @return int comparison
     */
    private static function sort($a, $b) {
        return $a['time'] < $b['time'];
    }
    
    /**
     * This function clears history
     */
    public static function clear() {
        $_SESSION[self::$cookie_key] = array();
    }
    
    // Sanity checking
    public static function dump() {
        var_dump($_SESSION[self::$cookie_key]);
    }
}
    