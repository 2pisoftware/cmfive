<?php

/**
 * 
 * This listener sets the top navigation bar!
 * @param unknown_type $w
 */
function main_listener_PRE_ACTION($w) {
	// global $modules; 
			
    // set the top navigation
    $nav = array();
    $redirect_url = "main/index";
    if ($w->Auth->loggedIn()) {
        // Redirect to users redirect_url
        if (!empty($w->Auth->user()->redirect_url)){
            $redirect_url = $w->Auth->user()->redirect_url;
        }

        // Filter out everything except the path so that users cant make redirect urls out of cmfive
        $parse_url = parse_url($redirect_url);
        $redirect_url = $parse_url["path"];

        // Menu link doesnt like a lead slash
        if ($redirect_url[0] == "/") {
            $redirect_url = substr($redirect_url, 1);
        }

        $nav[] = $w->menuLink($redirect_url, "Home");
        
        foreach ($w->modules() as $module) {
            if ($w->moduleConf($module, "topmenu") == true) {
                $w->menuLink($module."/index",ucfirst($module),$nav);
            }
        }
    }
    $w->ctx("top_navigation", $nav);
} 
