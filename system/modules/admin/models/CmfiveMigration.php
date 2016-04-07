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
	
	/**
	 * Helper methods
	 */
	
	/**
	 * Adds a column to a table. Takes care of checking for table/column existance
	 * 
	 * @param string $table
	 * @param string $column
	 * @param string $datatype
	 * @param Array $options
	 * @return null
	 */
	public function addColumnToTable($table, $column, $datatype, $options = []) {
		if ($this->hasTable($table)) {
			if (!$this->table($table)->hasColumn($column)) {
				$this->table($table)->addColumn($column, $datatype, $options)->save();
			}
		}
	}
	
	/**
	 * Removes a column from a table. Takes care of checking for table/column
	 * existance
	 * 
	 * @param string $table
	 * @param string $column
	 * @return null
	 */
	public function removeColumnFromTable($table, $column) {
		if ($this->hasTable($table)) {
			if ($this->table($table)->hasColumn($column)) {
				$this->table($table)->removeColumn($column);
			}
		}
	}
}

class MigrationException extends Exception {
	
}