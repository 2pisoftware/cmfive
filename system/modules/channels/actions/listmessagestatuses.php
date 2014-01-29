<?php

function listmessagestatuses_ALL(Web $w) {

	$p = $w->pathMatch("id");
	$id = $p["id"];
	$w->Channel->navigation("Message Statuses");

	if (!$id) {
		$w->error("Message ID not found", "/channels/listmessages");
	}

	$messagestatuses = $w->Channel->getMessageStatuses($id);

	$w->ctx("statuses", $messagestatuses);

}