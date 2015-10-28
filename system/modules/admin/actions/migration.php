<?php

function migration_GET(Web $w) {
	
//	$w->ctx("status", $w->Migration->runMigrations());
	
	$w->ctx('installed', $w->Migration->getInstalledMigrations());
	$w->ctx('available', $w->Migration->getAvailableMigrations());
	
}