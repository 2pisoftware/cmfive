<?php

function index_GET(Web $w) {

	$available = $w->Migration->getAvailableMigrations('all');
	$installed = $w->Migration->getInstalledMigrations('all');
	
	// Sort by modules that have a migration first, then alphabetically
	uksort($available, function($a, $b) use ($available) {
		if (count($available[$a]) > 0 && count($available[$b]) == 0) {
			return -1;
		} else if (count($available[$a]) == 0 && count($available[$b]) > 0) {
			return 1;
		} else {
			return strcmp($a, $b);
		}
	});
	
//	var_dump($available);
//	var_dump($installed);
	
	$w->ctx('installed', $installed);
	$w->ctx('available', $available);
	
}