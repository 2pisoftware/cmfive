<?php

require_once "Swift-4.1.7/lib/swift_required.php";

class MailService extends DbService {
	
	function sendMail($to, $from, $subject, $body, $cc = null, $bcc = null) {
		
	}
	
	function getSwiftMailer() {
		
	}
}