<?php 

function listtaskgroups_ALL(Web $w, $params = array()) {
	$taskgroups = $params['taskgroups'];
	$redirect = $params['redirect'];

	$w->ctx("taskgroups", $taskgroups);
}
