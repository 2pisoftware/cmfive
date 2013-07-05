<?php
class Inbox_message extends DbObject {
	var $digest;
	var $message;

	function insert() {
		$this->digest = sha1($this->message);
		parent::insert();
	}
}