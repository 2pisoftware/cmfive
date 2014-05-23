<?php
/**
 * This function gets called EVERY TIME before an action is
 * executed in the request handling
 * 
 * With access to $w you can inject any data into the context
 * or even catch the flow and redirect it to bypass the original
 * action.
 * 
 * BE CAREFUL! THE ERRORS YOU CAN CAUSE WITH THIS FUNCTION CAN BE
 * CATASTROPHIC AND HARD TO DEBUG!! 
 * 
 * YOU HAVE BEEN WARNED!
 * 
 * @param Web $w
 */
function example_listener_PRE_ACTION(Web $w) {
	// best not to do anything, unless you have to
}

/**
 * This function gets called EVERY TIME after an action has been
 * executed and before the template is evaluated and sent back to
 * the browser.
 * 
 * With access to $w you can inspect what happened and changed the
 * further cause of events, eg. send a notification or update
 * other parts of the database.
 * 
 * For your convenience there are various entries in the context:
 * 
 * $w->ctx("db_deletes") gives you an array of classes with list of id's of deleted objects
 * 
 * $w->ctx("db_updates") gives you an array of classes with list of id's of updated objects
 * 
 * $w->ctx("db_inserts") gives you an array of classes with list of id's of inserted objects
 * 
 * BE CAREFUL! THE ERRORS YOU CAN CAUSE WITH THIS FUNCTION CAN BE
 * CATASTROPHIC AND HARD TO DEBUG!! 
 * 
 * YOU HAVE BEEN WARNED!
 * 
 * @param Web $w
 */
function example_listener_POST_ACTION(Web $w) {
	// best not to do anything, unless you have to
}