<?php
/**
 * delete an ExampleData
 *
 * Url:
 *
 * /example/delete/{id}
 *
 * @param Web $w
 */
function delete_GET(Web $w) {
	// parse url for object id
	$p = $w->pathMatch("id");
	
	if (!empty($p['id'])) {
		
		// get the object
		$d = $w->Example->getDataForId($p['id']);
		if (!empty($d)) {
			
			// delete (if is_deleted property exists, this will be set to "1")
			$d->delete();
			
			// return to the list
			$w->msg("Object Deleted","/example/index");
		}
	}
	
	// in error, display this straight to layout
	$w->out("This object does not exist.");
}