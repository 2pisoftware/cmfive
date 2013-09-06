<?php
class WikiPageHistory extends WikiPage {
	
	// remove the searchable aspect which was defined
	// in the parent class
	public $_remove_searchable;
	
	public $wiki_page_id;

	function update() {
		DbObject::update();
	}

	function insert() {
		DbObject::insert();
	}

	function getDbTableName() {
		return "wiki_page_history";
	}
}