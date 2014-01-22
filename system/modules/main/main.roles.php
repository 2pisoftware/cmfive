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


?>
