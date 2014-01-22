<?php

class EmailChannelOption extends DbObject {
	static $_db_table = "channel_email_option";
	public $_channeltype = "email";

	public $channel_id;
	public $server;
	public $s_username;
	public $s_password;
	public $port;
	public $use_auth;

	public $folder;

	public function __construct(Web $w) {
		parent::__construct($w);
		$this->setPassword(hash("md5", $w->moduleConf("channels", "__password")));
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

	public function doJob() {

		// Open email connection
		$connection = imap_open("{pop.gmail.com:995}", $this->s_username, $this->s_password);
		$count = imap_num_msg($connection);

		var_dump($count);

	}	

}