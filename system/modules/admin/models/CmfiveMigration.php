<?php

require "CmfiveTable.php";

use \Cmfive\Table as Table;
use Phinx\Db\Table\Column as Column;

class CmfiveMigration extends Phinx\Migration\AbstractMigration {

	public function Column() {
		return new Column;
	}
	
	public function table($tableName, $options = array()) {
		return new Table($tableName, $options, $this->getAdapter());
	}
	
	public function tableWithId($tablename) {
		$id = $this->Column();
		$id->setName('id')
		->setType('biginteger')
		->setIdentity(true);
		
		return $this->table($tablename, [
			'id' => false,
			'primary_key' => 'id'
		])->addColumn($id);
	}
	
	// To preserve the table/data
	public function dropTable($tableName) {
		if ($this->hasTable($tableName)) {
			$table = $this->table($tableName);
			$table->rename(date('YmdHis') . $tableName);
		}

//		parent::dropTable($tableName);
	}

}

class MigrationException extends Exception {
	
}