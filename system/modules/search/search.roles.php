<?php

function role_search_allowed(Web $w,$path) {
	return $w->checkUrl($path, "search", "*", "*");
}