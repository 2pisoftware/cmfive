<?php
/**
 * This is an example hook function. It will be called from within the 
 * DbObject::delete() method, if performed on an ExampleData object.
 * 
 * Read DbObject::delete() and other DbObject methods to find out where
 * you can hook into.
 * 
 * To make this hook active, it has to be declared in this module's config.php.
 * 
 * @param Web $w
 * @param ExampleData $object
 */
function example_core_dbobject_after_delete_ExampleData(Web $w, ExampleData $object) {
	// don't do anything here unless you have to!
}

/**
 * This is an example of a hook that adds things to a screen.
 * Look at /example/actions/index.php where this is called.
 * 
 * @param Web $w
 * @param array $actions
 * @return array the passed in array of actions plus any additional actions
 */
function example_example_add_row_action(Web $w, $params) {
	$data = $params['data'];
	$actions = $params['actions'];
	$actions[] = Html::b("","HOOK for id #".$data->id);
	return $actions;
}
