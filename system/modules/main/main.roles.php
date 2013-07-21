<?php

function role_user_allowed(&$w,$path) {
    $include = array(
        "main",
        "auth",
    );
    $path_explode = explode("/", $path);
    $module = $path_explode[0];
    $action = $path_explode[1];
    $allowed = in_array($module,$include);
    return $allowed;
}


/**
 * This role is called when no user is logged in!
 * 
 * @param <type> $w
 * @return <type>
 */
function anonymous_allowed(&$w,$path) {
    // First check by specific IP addresses!
    // this is useful for scripts to be executed via cron jobs
    
    $ips = array(
    );
    if( in_array($w->requestIpAddress(),$ips)) {
        return true;
    }

    // check include paths for people
    $include = array(
        "auth/login",
        "auth/forgotpassword"
    );    
    $in_path = in_array($path,$include);

    // check complete modules
    $modules = array(
    );
    $path_explode = explode("/", $path);
    $module = $path_explode[0];
    $action = $path_explode[1];
    $allowed = in_array($module,$modules);
    
    return $allowed || $in_path || $has_ip;
}


?>
