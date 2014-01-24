<?php 

function edit_GET(Web $w) {

	$p = $w->pathMatch("id");
	$channel_id = $p["id"];

	$w->Channel->navigation($channel_id ? "Edit" : "Add" . " an Email Channel");

	// Get channel and form
	$channel_object = $channel_id ? $w->Channel->getChannel($channel_id) : new Channel($w);
	$form = $channel_object->getForm();

	$email_channel = $channel_id ? $w->Channel->getEmailChannel($channel_id) : new EmailChannelOption($w);
	// $folder_list = $email_channel->getFolderList();
	// Decrypt username and password
	$email_channel->decrypt();

	$form["Email"] = array(
		array(
			array("Protocol", "select", "protocol", $email_channel->protocol, $email_channel::$_select_protocol)
		),
		array(
			array("Server URL", "text", "server", $email_channel->server)
		),
		array(
			array("Username", "text", "s_username", $email_channel->s_username),
			array("Password", "password", "s_password", $email_channel->s_password)
		),
		array(
			array("Port", "text", "port", $email_channel->port),
			array("Use Auth?", "checkbox", "use_auth", $email_channel->use_auth)
		),
		array(
			array("Folder", "text", "folder", $email_channel->folder)
		),
	);

	$form["Filter"] = array(
		array(
			array("To", "text", "to_filter", $email_channel->to_filter),
			array("From", "text", "from_filter", $email_channel->from_filter)
		),
		array(
			array("CC", "text", "cc_filter", $email_channel->cc_filter),
			array("Subject", "text", "subject_filter", $email_channel->subject_filter)
		),
		array(
			array("Body", "text", "body_filter", $email_channel->body_filter)
		)
	);

	$form["Action"] = array(
		array(
			array("Post Read Action", "select", "post_read_action", $email_channel->post_read_action, $email_channel::$_select_read_action),
			array("Post Read Data", "text", "post_read_parameter", $email_channel->post_read_parameter)
		)
	);

	$w->ctx("form", Html::multiColForm($form, "/channels-email/edit/{$channel_id}", "POST", "Save", "channelform"));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	$channel_id = $p["id"];

	$channel_object = $channel_id ? $w->Channel->getChannel($channel_id) : new Channel($w);
	$channel_object->fill($_POST);
	$channel_object->insertOrUpdate();

	$email_channel = $channel_id ? $w->Channel->getEmailChannel($channel_id) : new EmailChannelOption($w);
	$email_channel->fill($_POST);
	$email_channel->channel_id = $channel_object->id;
	$email_channel->insertOrUpdate();

	$w->msg("Email Channel " . ($channel_id ? "updated" : "created"), "/channels/listchannels");

}