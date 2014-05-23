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
function crm_core_dbobject_after_delete_ExampleData(Web $w, ExampleData $object) {
	
}
