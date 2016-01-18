<?php namespace Cmfive;

class Table extends \Phinx\Db\Table {
	
	public function addCmfiveParameters($exclude = []) {
		// dt_created
		if (!in_array("dt_created", $exclude)) {
			$this->addColumn("dt_created", "datetime", ['null' => true]);
		}
		
		// dt_modified
		if (!in_array("dt_modified", $exclude)) {
			$this->addColumn("dt_modified", "datetime", ['null' => true]);
		}
		
		// creator_id
		if (!in_array("creator_id", $exclude)) {
			$this->addColumn("creator_id", "biginteger", ['null' => true]);
		}
		
		// modifier_id
		if (!in_array("modifier_id", $exclude)) {
			$this->addColumn("modifier_id", "biginteger", ['null' => true]);
		}
		
		// is_deleted
		if (!in_array("is_deleted", $exclude)) {
			$this->addColumn("is_deleted", "boolean", ["default" => false]);
		}
		
		return $this;
	}
	
}