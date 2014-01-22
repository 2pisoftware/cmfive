<?php

function callchannel_ALL(Web $w) {

	$p = $w->pathMatch("id");
	$id = $p["id"];

	$channel = $w->Channel->getEmailChannel($id);
	$channel->doJob();

}