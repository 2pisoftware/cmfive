<?php
function role_favorites_admin_allowed($w, $path) {
	return startsWith($path, "favorites");
}
