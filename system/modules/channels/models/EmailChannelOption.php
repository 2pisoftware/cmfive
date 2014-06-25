<?php

use \Zend\Mail\Storage as Zend_Mail_Storage;
use \Zend\Mail\Message as Zend_Mail_Message;
use \Zend\Mail\Storage\Imap as Zend_Mail_Storage_Imap;

class EmailChannelOption extends DbObject {

    static $_db_table = "channel_email_option";
    public $_channeltype = "email";
    public $channel_id;
    public $server;
    public $s_username;
    public $s_password;
    public $port;
    public $use_auth;
    public $protocol; // pop3, imap
    static public $_select_protocol = array("POP3", "IMAP");
    public $subject_filter;
    public $to_filter;
    public $from_filter;
    public $cc_filter;
    public $body_filter;
    public $folder;
    public $post_read_action; // delete, mark as archived, move to folder, apply tag, forward to email
    static public $_select_read_action = array("Archive", "Move to Folder", "Apply Tag", "Forward to", "Delete");
    public $post_read_parameter; // stores extra data, eg. tag name, folder name, forward email, etc.

    public function __construct(Web $w) {
        parent::__construct($w);
        $this->setPassword(hash("md5", $w->moduleConf("channels", "__password")));
    }

    public function delete($force = false) {
        $channel = $this->getChannel();
        $channel->delete($force);

        parent::delete($force);
    }

    public function getChannel() {
        if (!empty($this->channel_id)) {
            return $this->w->Channel->getChannel($this->channel_id);
        }
        return null;
    }

    public function getNotifyUser() {
        $channel = $this->getChannel();
        if (!empty($channel)) {
            return $channel->getNotifyUser();
        }
    }

    public function read() {
        // Setup filter array
        $filter_arr = array();
        // TO
        if (!empty($this->to_filter)) {
            $filter_arr[] = "TO " . $this->to_filter;
        }
        // FROM
        if (!empty($this->from_filter)) {
            $filter_arr[] = "FROM " . $this->from_filter;
        }
        // CC
        if (!empty($this->cc_filter)) {
            $filter_arr[] = "CC " . $this->cc_filter;
        }
        // SUBJECT
        if (!empty($this->subject_filter)) {
            $filter_arr[] = "SUBJECT " . $this->subject_filter;
        }
        // BODY
        if (!empty($this->body_filter)) {
            $filter_arr[] = "BODY " . $this->body_filter;
        }
        // UNSEEN
        $filter_arr[] = "UNSEEN";

        // Connect and fetch emails
        $this->w->Log->info("Connecting to mail server");
        $mail = $this->connectToMail();
        if (!empty($mail)) {
            
            $this->w->Log->info("Getting messages with filter: " . json_encode($filter_arr));
            $results = $mail->protocol->search($filter_arr);
            if (count($results) > 0) {
                $this->w->Log->info("Found " . count($results) . " messages, looping through");
                foreach ($results as $messagenum) {
                    $rawmessage = "";
                    $message = $mail->getMessage($messagenum);
                    $zend_message = new Zend_Mail_Message();
                    $zend_message->setHeaders($message->getHeaders());
                    $zend_message->setBody($message->getContent());

//                     $rawmessage .= $message->getHeaders()->toString();
//                     // 
//                     $rawmessage .= "\n\n";
//                     $rawmessage .= $message->getContent();
                    $email = new EmailStructure();
                    $email->to = $message->to;
                    $email->from = $message->from;
                    if (isset($message->cc)) {
                        $email->cc = $message->cc;
                    }
                    $email->subject = $message->subject;
                    //$email->body["html"] = $message->getContent();

                    $rawmessage .= $zend_message->toString();

                    // Create messages
                    $channel_message = new ChannelMessage($this->w);
                    $channel_message->channel_id = $this->channel_id;
                    $channel_message->message_type = "email";
                    // $channel_message->attachment_id = $attachment_id;
                    $channel_message->is_processed = 0;
                    $channel_message->insert();

                    // Save raw email
                    $attachment_id = $this->w->File->saveFileContent($channel_message, $rawmessage, str_replace(".", "", microtime()) . ".txt", "channel_email_raw", "text/plain");
                    if ($message->isMultipart()) {
                        foreach (new RecursiveIteratorIterator($message) as $part) {
                            try {
                                $contentType = strtok($part->contentType, ';');
                                switch ($contentType) {
                                    case "text/plain":
                                        $email->body["plain"] = trim($part->__toString());
                                        break;
                                    case "text/html":
                                        $email->body["html"] = trim($part->__toString());
                                        break;
                                    default:
                                        // Is probably an attachment so just save it
                                        $transferEncoding = $part->getHeader("Content-Transfer-Encoding")->getFieldValue("transferEncoding");
                                        $content_type_header = $part->getHeader("Content-Type");
                                        // Name is stored under "parameters" in an array
                                        $nameArray = $content_type_header->getParameters();
                                        $this->w->File->saveFileContent($channel_message, 
                                                ($transferEncoding == "base64" ? base64_decode(trim($part->__toString())) : trim($part->__toString())) , 
                                                !empty($nameArray["name"]) ? $nameArray["name"] : ("attachment" . time()), 
                                                "channel_email_attachment", 
                                                $contentType);
                                }
                            } catch (Zend_Mail_Exception $e) {
                                // Ignore
                            }
                        }
                    }

                    $attachment_id = $this->w->File->saveFileContent($channel_message, serialize($email), "email.txt", "channel_email_raw", "text/plain");
                }
            } else {
                $this->w->Log->info("No new messages found");
            }
        }
    }

    private function connectToMail($shouldDecrypt = true) {
        if ($shouldDecrypt) {
            $this->decrypt();
        }

        try {
            // Open email connection
            $mail = new Zend_Mail_Storage_Imap(array('host' => $this->server,
                'user' => $this->s_username,
                'password' => $this->s_password,
                'ssl' => ($this->use_auth == 1 ? "SSL" : false)));
            return $mail;
        } catch (Exception $e) {
            $this->Log->error("Error connecting to mail server: " . $e->getMessage());
        }
    }

    public function getFolderList($shouldDecrypt = true) {
        $mail = $this->connectToMail($shouldDecrypt);
        $folders = array();

        if (!empty($mail)) {
            if ($mail) {
                foreach ($mail->getFolders() as $mailfolder) {
                    foreach ($mailfolder as $folder) {
                        $folders[] = $folder->__toString();
                    }
                }
            }
        }
        return $folders;
    }

}
