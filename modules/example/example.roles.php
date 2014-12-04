<?php
function role_example_admin_allowed($w, $path) {
	return startsWith($path, "example");
}

function role_example_view_allowed($w, $path) {
	$actions = "/example\/(index";
    $actions .= "|view";
    $actions .= ")/";
    return preg_match($actions, $path);
}