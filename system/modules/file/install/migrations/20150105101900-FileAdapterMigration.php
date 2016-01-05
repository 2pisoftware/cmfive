<?php

class FileAdapterMigration extends CmfiveMigration {
	
	public function up() {
		
		if ($this->hasTable("file")) {
			if (!$this->table("file")->hasColumn("adapter")) {
				$this->table("file")->addColumn("adapter", "string", ["default" => "local", "limit" => 255])->save();
			}
		}
		
	}
	
	public function down() {
		
		$this->hasTable("file") && $this->table("file")->hasColumn("adapter") ? $this->table("file")->dropColumn("adapter") : null;
		
	}
	
}


/**
 * SQL: ALTER TABLE `attachment` ADD `adapter` VARCHAR(255) NOT NULL DEFAULT 'local' ;
 */