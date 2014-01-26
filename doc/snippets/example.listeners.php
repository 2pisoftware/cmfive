<?php

/**
 * This function is called BEFORE the action is executed
 * 
 * @param unknown $w
 */
function example_listener_PRE_ACTION(Web $w) {
	// you can add to or change the context
	$w->ctx("key",$value);
	
	// you can redirect the request.. but maybe you shouldn't!
	$w->redirect("/main");
}

/**
 * This function is called AFTER the action was executed
 * 
 * @param unknown $w
 */
function example_listener_POST_ACTION(Web $w) {
	// you can find out which objects have changed
	$updated = $w->ctx("db_updated"); // returns array("classname" => array($id1, $id2, ..), ..);
	$deleted = $w->ctx("db_deleted"); // returns array("classname" => array($id1, $id2, ..), ..);
	$inserts = $w->ctx("db_inserts"); // returns array("classname" => array($id1, $id2, ..), ..);
	
	// you can redirect the request.. but maybe you shouldn't!
	$w->redirect("/main");	
}