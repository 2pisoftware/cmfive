<?php

/**
 * 
 * This listener sets the top navigation bar!
 * @param unknown_type $w
 */
function main_listener_PRE_ACTION($w) {
	global $modules;
			
    // set the top navigation
    $nav = array();
    if ($w->Auth->loggedIn()) {
        $nav[]=$w->menuLink("main/index","Home");
        
        foreach ($modules as $name => $options) {
        	if ($options['topmenu']) {
            	$w->menuLink($name."/index",ucfirst($name),$nav);
        	}
        }
    }
    $w->ctx("top_navigation", $nav);
} 
