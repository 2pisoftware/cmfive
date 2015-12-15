<?php
//
// in the class definition add the following properties and functions
//

/**
 * file: /modules/<name>/models/Example.php
 * 
 * @author careck
 *
 */
class Example extends DbObject {
	
	// add this to the search index
	public $_searchable;
	
	// add custom content to search index
	public function addToIndex() {
		return $content;
	}
	
	// format display
	public function printSearchTitle() {
		return $title;
	}
	public function printSearchListing() {
		return $listing;
	}
	public function printSearchUrl() {
		return $url;
	}
	
	// does this show in the search results?
	public function canList(User $user = null) {
		return true;
	}
	
	// does this display the details link?
	public function canView(User $user = null) {
		return true;
	}
}

//
// in the config.php file add the following key
//

$modules['<name>']['search'] = array(
		"<Index Title>" => "Example",
);