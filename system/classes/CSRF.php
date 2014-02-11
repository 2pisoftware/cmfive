<?php 
/**
 * Static class to generate and check CSRF tokens
 * Based on example found http://www.wikihow.com/Prevent-Cross-Site-Request-Forgery-(CSRF)-Attacks-in-PHP
 */
class CSRF {
    private static $token_id_name = "token_id";
    private static $token_value_name = "token_value";

    /**
     * Generates new CSRF token_id and store it in the $_SESSION.
     * 
     * Returns token_id.
     * 
     * @return string
     */
    public static function getTokenID() {
        if(!isset($_SESSION[self::$token_id_name])) { 
            $_SESSION[self::$token_id_name] = self::random(10);
        }

        return $_SESSION[self::$token_id_name];
    }

    /**
     * Generates new CSRF token_value and store it in the $_SESSION.
     * 
     * Returns token_value.
     * 
     * @return string
     */
    public static function getTokenValue() {
        if(!isset($_SESSION[self::$token_value_name])) { 
            $_SESSION[self::$token_value_name] = hash('sha256', self::random(500));
        }

        return $_SESSION[self::$token_value_name];
    }

    /**
     * Regenerates the CSRF tokens, useful for preventing mulitple
     * form submissions.
     */
    public static function regenerate() {
        // Unset session variables
        if (isset($_SESSION[self::$token_id_name])) {
            unset($_SESSION[self::$token_id_name]);
        }
        if (isset($_SESSION[self::$token_value_name])) {
            unset($_SESSION[self::$token_value_name]);
        }

        // Create new key/value
        self::getTokenID();
        self::getTokenValue();
    }

    /**
     * Check whether or not the token value passed in $method (GET/POST..) 
     * match the token stored in the $_SESSION.
     * 
     * @param string $method
     * @return boolean
     */
    public static function isValid($method) {
        $method = strtolower($method);
        
        // Allow get through for now
        if ($method === "get") {
            return true;
        }

        // Restrict access to post
        if ($method == "post") {
            $get = $_GET; $post = $_POST;

            if(isset(${$method}[self::getTokenID()])) {
                return (${$method}[self::getTokenID()] == self::getTokenValue());
            }
        }

        return false;
    } 

    /**
     * A better random function?
     * 
     * @param integer $len
     * @return string
     */
    private static function random($len) {
        if (@is_readable('/dev/urandom')) {
            $f=fopen('/dev/urandom', 'r');
            $urandom=fread($f, $len);
            fclose($f);
        }

        $return='';
        for ($i=0;$i<$len;++$i) {
            if (!isset($urandom)) {
                if ($i%2==0) mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
                $rand=48+mt_rand()%64;
            } else $rand=48+ord($urandom[$i])%64;

            if ($rand>57) $rand+=7;
            if ($rand>90) $rand+=6;
            if ($rand==123) $rand=52;
            if ($rand==124) $rand=53;
            $return.=chr($rand);
        }
        return $return;
    }
}