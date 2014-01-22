<?php

function listprocessors_GET(Web $w) {
	$w->Channel->navigation("Processors List");
	// Get all email, FTP, local processors
	$processors = $w->Channel->getProcessors();

	$w->ctx("processors", $processors);
}