<?php

class EmailChannelOption extends DbObject {
	static $_db_table = "channel_email_option";
	
	public $channel_id;
	public $server;
	public $s_username;
	public $s_password;
	public $port;
	public $use_auth;

	public $folder;

}