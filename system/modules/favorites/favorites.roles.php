<?php
/**
 * favorites user roles 
 *
 * @author Steve Ryan, steve@2pisystems.com, 2015
 **/

function role_favorites_user_allowed($w, $path) {
	return startsWith($path, "favorites");
}
