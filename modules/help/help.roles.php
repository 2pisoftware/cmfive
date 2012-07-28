<?php
function role_help_view_allowed(&$w,$path) {
	    return preg_match("/help(-.*)?\//",$path);
}

function role_help_contact_allowed(&$w,$path) {
	    return preg_match("/help(-.*)?\//",$path);
}