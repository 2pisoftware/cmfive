<?php
function role_favorites_user_allowed($w, $path) {
	return startsWith($path, "favorites");
}
