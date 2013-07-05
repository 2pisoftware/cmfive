<?php
class Lookup extends DbObject {
	var $weight;
	var $type;
	var $code;
	var $title;
	var $is_deleted;

	function getDbTableName() {
		return "lookup";
	}
}