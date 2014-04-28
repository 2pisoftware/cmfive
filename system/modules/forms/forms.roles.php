<?php
function role_forms_admin_allowed(Web $w,$path) {
	return preg_match("/forms(-.*)?\//",$path);
}

function role_forms_editor_allowed(Web $w,$path) {
	return preg_match("/forms\//",$path);
}

function role_forms_user_allowed(Web $w,$path) {
	return preg_match("/forms\//",$path);
}
