<?php

class MailService extends DbService {

	public function sendMail($to, $from, $subject, $body, $cc = null, $bcc = null) {
		global $EMAIL_CONFIG;

		// Instantiate transport
		$transport = Swift_SmtpTransport::newInstance($EMAIL_CONFIG["host"], $EMAIL_CONFIG["port"], 'ssl')
			->setUsername($EMAIL_CONFIG["username"])
			->setPassword($EMAIL_CONFIG["password"]);

		// Set SSL if auth present 
		// if ($EMAIL_CONFIG["auth"] === true) {
		// 	$transport->setEncryption("ssl");
		// }
		$mailer = Swift_Mailer::newInstance($transport);

		// Create message
		$message = Swift_Message::newInstance($subject)
			->setFrom($from)->setTo($to)
			->setBody($body)->addPart($body, 'text/html');
 		if (!empty($cc)) {
 			$message->setCc($cc);
 		}
 		if (!empty($bcc)) {
 			$message->setBcc($bcc);
 		}

 		// Send
 		$result = $mailer->send($message, $failures);
 		if (!$result) {
 			
 		}
 		return $result;
	}
	
	private function getLayer($layer = "swiftmailer") {
		switch($layer){
			case "swiftmailer":
				
				return $transport;
			break;
			case "mail":
				// Do something 
			break;
		}
	}
}