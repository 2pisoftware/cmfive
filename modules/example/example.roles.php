<?php
function role_example_admin_allowed($w, $path) {
	return startsWith($path, "example");
}