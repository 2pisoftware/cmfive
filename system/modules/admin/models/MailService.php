<?php

class MailService extends DbService {
	
	private $transport;
	private static $logger = 'MAIL';
        
	public function __construct(Web $w) {
		parent::__construct($w);
		$this->initTransport();
	}
	
    /**
     * Sends an email using config array from /config.php and the swiftmailer lib
     * for transport. 
     * 
     * @param string $to
     * @param string $replyto
     * @param string $subject
     * @param string $body
     * @param string $cc (optional)
     * @param string $bcc (optional)
     * @param Array $attachments (optional)
     * @return int
     */
    public function sendMail($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = array()) {
        if (!empty($to) && strlen($to)>0) {
			try {
				if ($this->transport === NULL) {
					$this->w->Log->error("Could not send mail to {$to} from {$replyto} about {$subject} no email transport defined!");
					return;
				}

				$mailer = Swift_Mailer::newInstance($this->transport);

				// To, cc, bcc need to be given as arrays when sending to more than one person
				// Ie you separate them by a comma, this will split them into arrays as expected by Swift
				if (strpos($to, ",") !== FALSE) {
					$to = array_map("trim", explode(',', $to));
				}
				
				// Create message
				$message = Swift_Message::newInstance($subject)
								->setFrom($replyto)
								->setTo($to)->setBody($body)
								->addPart($body, 'text/html');
				if (is_array($replyto)) {
					$message->setReplyTo($replyto);
				} else {
					$message->setReplyTo(array($replyto));
				}
				if (!empty($cc)) {
					if (strpos($cc, ",") !== FALSE) {
						$cc = array_map("trim", explode(',', $cc));
					}
					$message->setCc($cc);
				}
				if (!empty($bcc)) {
					if (strpos($bcc, ",") !== FALSE) {
						$bcc = array_map("trim", explode(',', $bcc));
					}
					$message->setBcc($bcc);
				}

				// Add attachments
				if (!empty($attachments)) {
					foreach ($attachments as $attachment) {
						if (!empty($attachment)) {
							$message->attach(Swift_Attachment::fromPath($attachment));
						}
					}
				}

				$this->w->Log->setLogger(MailService::$logger)->info("Sending email to {$to} from {$replyto} with {$subject} (" . count($attachments) . " attachments");
				$mailer_status = $mailer->send($message, $failures);
				if (!empty($failures)) {
					$this->w->Log->setLogger(MailService::$logger)->error("Failed to send email: " . serialize($failures));
				}
			} catch (Exception $e) {
				$this->w->Log->setLogger(MailService::$logger)->error("Failed to send email: " . $e);
			}
			// failure to end
			return 1;
		}
    }

    private function initTransport() {
        $layer = Config::get('email.layer');
        
        // Set default layer if it doesn't exist
        if (empty($layer)) {
            $layer = "sendmail";
        }
        
        switch ($layer) {
            case "smtp":
            case "swiftmailer":
                    $this->transport = Swift_SmtpTransport::newInstance(Config::get('email.host'), Config::get('email.port'), Config::get('email.auth') == true ? 'ssl' : null)
                ->setUsername(Config::get('email.username'))
                ->setPassword(Config::get('email.password'));
            break;
            case "sendmail":
                $command = Config::get('email.command');
                if (!empty($command)) {
                    $this->transport = Swift_SendmailTransport::newInstance(Config::get('email.command'));
                } else {
                    $this->transport = Swift_SendmailTransport::newInstance();
                }
            break;
        }
    }

}
