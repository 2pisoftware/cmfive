<?php

function index_GET(Web $w) {

	$available = $w->Migration->getAvailableMigrations('all');
	$installed = $w->Migration->getInstalledMigrations('all');
	
	$batched = [];
	if (!empty($installed)) {
		foreach($installed as $module => $install) {
			foreach($install as $migration_as_array) {
				$batched[$migration_as_array['batch']][] = $migration_as_array;
			}
		}
	}
	
	$not_installed = [];
	if (!empty($available)) {
		foreach($available as $module => $_available) {
			foreach($_available as $file => $class) {
				if (!$w->Migration->isInstalled($class)) {
					$not_installed[$module][$file] = $class;
				}
			}
		}
	}
	
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
	
	$w->ctx('batched', $batched);
	$w->ctx('not_installed', $not_installed);
	$w->ctx('installed', $installed);
	$w->ctx('available', $available);
	
}