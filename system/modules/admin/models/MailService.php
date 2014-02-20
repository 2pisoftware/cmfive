<?php

class MailService extends DbService {

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
        global $EMAIL_CONFIG;

        // Instantiate transport
        $transport = Swift_SmtpTransport::newInstance($EMAIL_CONFIG["host"], $EMAIL_CONFIG["port"], 'ssl')
                ->setUsername($EMAIL_CONFIG["username"])
                ->setPassword($EMAIL_CONFIG["password"]);

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

        // Add attachments
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $message->attach(Swift_Attachment::fromPath($attachment));
            }
        }

        // Send
        $result = $mailer->send($message, $failures);
        if (!$result) {
            
        }
        return $result;
    }

    private function getLayer($layer = "swiftmailer") {
        switch ($layer) {
            case "swiftmailer":

                return $transport;
                break;
            case "mail":
                // Do something 
                break;
        }
    }

}
