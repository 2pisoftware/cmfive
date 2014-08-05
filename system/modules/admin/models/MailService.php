<?php

class MailService extends DbService {
	
	private $transport;
	
	public function __construct(Web $w) {
		parent::__construct($w);
		$this->initTransport();
	}
	
    /**
     * Sends an email using config array from /config.php and the swiftmailer lib
     * for transport. 
     * 
     * @global Array $EMAIL_CONFIG
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $body
     * @param string $cc (optional)
     * @param string $bcc (optional)
     * @param Array $attachments (optional)
     * @return int
     */
    public function sendMail($to, $from, $subject, $body, $cc = null, $bcc = null, $attachments = array()) {
        
        if ($this->transport === NULL) {
        	$this->w->Log->error("Could not send mail to {$to} from {$from} about {$subject} no email transport defined!");
        	return;
        }

        $mailer = Swift_Mailer::newInstance($this->transport);

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

        // Add attachments
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
            	if (!empty($attachment)) {
                	$message->attach(Swift_Attachment::fromPath($attachment));
            	}
            }
        }

        return $mailer->send($message, $failures);
    }

    private function initTransport() {
        $layer = Config::get('email.layer');
        switch ($layer) {
            case "smtp":
            case "swiftmailer":
                    $this->transport = Swift_SmtpTransport::newInstance(Config::get('email.host'), Config::get('email.port'), 'ssl')
                ->setUsername(Config::get('email.username'))
                ->setPassword(Config::get('email.password'));
            break;
            case "sendmail":
                $command = Config::get('email.command');
                //
                // empty() is a language construct and cannot deal with return values from functions!
                //
                if (!empty($command)) {
                    $this->transport = Swift_SendmailTransport::newInstance(Config::get('email.command'));
                } else {
                    $this->transport = Swift_SendmailTransport::newInstance();
                }
            break;
        }
    }

}
