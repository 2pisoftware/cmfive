<?php

class FileInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		$this->table('attachment', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('parent_table', 'string', ['limit' => 255])
				->addColumn('obj_id', 'biginteger')
				->addColumn('modifier_user_id', 'biginteger', ['null' => true])
				->addColumn('filename', 'string', ['limit' => 255])
				->addColumn('mimetype', 'string', ['limit' => 255, 'null' => true])
				->addColumn('title', 'string', ['limit' => 255, 'null' => true])
				->addColumn('description', 'text')
				->addColumn('fullpath', 'text')
				->addColumn('type_code', 'string', ['limit' => 255, 'null' => true])
				->addCmfiveParameters()
				->create();
		
		$this->table('attachment_type', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('table_name', 'string', ['limit' => 255])
				->addColumn('code', 'string', ['limit' => 255])
				->addColumn('title', 'string', ['limit' => 255])
				->addColumn('is_active', 'boolean', ['default' => 1])
				->create();
	}

	public function down() {
		$this->dropTable('attachment');
		$this->dropTable('attachment_type');
	}

}
