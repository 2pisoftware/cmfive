<?php
class Inbox_message extends DbObject {
	public $digest;
	public $message;

	function insert() {
		$this->digest = sha1($this->message);
		parent::insert();
	}
}