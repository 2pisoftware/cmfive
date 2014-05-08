<?php
function role_comment_allowed(Web $w,$path) {
	$actions = array(
			"admin/comment",
			"admin/deletecomment",
	);
	return in_array($path, $actions);
}
