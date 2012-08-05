<?php
class WikiPageHistory extends WikiPage {
	var $wiki_page_id;

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