<?php
function role_forms_admin_allowed(&$w,$path) {
	return preg_match("/forms(-.*)?\//",$path);
}

function role_forms_editor_allowed(&$w,$path) {
	return preg_match("/forms\//",$path);
}

function role_forms_user_allowed(&$w,$path) {
	return preg_match("/forms\//",$path);
}
