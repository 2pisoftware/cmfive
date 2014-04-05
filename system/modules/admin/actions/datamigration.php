<?php

/**
 * This action kicks off the Data Migration classes for all modules
 * 
 * In the future, there should be a mechanism to prevent un-authorised access.
 * 
 * For now you will have to disable the action in the config file.
 * 
 */

@require ROOT."/system/classes/DbMigration.php";

function datamigration_GET(Web $w) {
	
	// go through all modules and find the migration file
	
}