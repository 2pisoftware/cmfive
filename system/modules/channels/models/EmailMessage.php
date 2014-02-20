<?php

use \Zend\Mail\Message as Zend_Mail_Message;

/**
 * Fascade Message class to parse raw email messages for a processor
 */
class EmailMessage extends DbService {

	private $_rawdata;

	public function __construct($rawdata) {
		$this->_rawdata = $rawdata;
	}

	public function parse() {
		$email = Zend_Mail_Message::fromString($this->_rawdata); //new Zend_Mail_Message(array('raw' => $this->_rawdata));
		// Do we need to do anything? Maybe get out attachements?
		return $email;
	}

}
