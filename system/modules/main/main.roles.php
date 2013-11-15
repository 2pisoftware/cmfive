<?php

function role_user_allowed(&$w,$path) {
    $include = array(
        "main",
        "auth",
    );
    $path_explode = explode("/", $path);
    $module = $path_explode[0];
    // $action = !empty($path_explode[1]) ? $path_explode;
    $allowed = in_array($module,$include);
    return $allowed;
}


/**
 * This role is called when no user is logged in!
 * 
 * Control access by IP, Module or Action in the
 * global /config.php using the global parameters provided.
 * 
 * $ALLOW_FROM_IP
 * 
 * $ALLOW_ACTION
 * 
 * $ALLOW_MODULE
 * 
 * @param <type> $w
 * @return boolean
 */
function anonymous_allowed(&$w,$path) {
    
	global $ALLOW_FROM_IP;
    if( in_array($w->requestIpAddress(),$ALLOW_FROM_IP)) {
        return true;
    }
 
    global $ALLOW_ACTION;
    $in_path = in_array($path,$ALLOW_ACTION);

	global $ALLOW_MODULE;
    $path_explode = explode("/", $path);
    $module = $path_explode[0];
    $action = $path_explode[1];
    $allowed = in_array($module,$ALLOW_MODULE);
    
    return $allowed || $in_path;
}


?>
