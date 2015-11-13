<?php
class Inbox_message extends DbObject {
	public $digest;
	public $message;

	function insert($force_validation = false) {
		$this->digest = sha1($this->message);
		parent::insert();
	}
}