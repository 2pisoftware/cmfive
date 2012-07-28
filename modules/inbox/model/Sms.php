<?php
class Sms extends DbObject {
	var $phone;
	var $message;
	var $dt_created;
	var $creator_id;
	
	function getDbTableName() {
		return "sms";
	}
	
	function send() {
		sendSMS(array($this->phone),$this->message,$this->w->auth->user()->login);
		
		// always store a fresh line item
		$this->dt_created = null;
		$this->creator_id = null;
		$this->id = null;
		$this->insert();
	}
}