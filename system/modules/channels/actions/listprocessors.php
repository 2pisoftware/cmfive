<?php

function listprocessors_GET(Web $w) {
	$w->Channels->navigation($w, "Processors List");
	// Get all email, FTP, local processors
	$processors = $w->Channel->getProcessors();

	$w->ctx("processors", $processors);
}