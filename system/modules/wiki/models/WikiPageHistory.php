<?php
class WikiPageHistory extends WikiPage {
	
	// remove the searchable aspect which was defined
	// in the parent class
	public $_remove_searchable;
	
	public $wiki_page_id;

	function update($force_null_values = false, $force_validation = false) {
		DbObject::update();
	}

	function insert($force_validation = false) {
		DbObject::insert();
	}

	function getDbTableName() {
		return "wiki_page_history";
	}
}