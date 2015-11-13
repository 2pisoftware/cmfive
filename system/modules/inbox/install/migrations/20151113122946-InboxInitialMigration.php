<?php

class InboxInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		$this->table('inbox', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('user_id', 'biginteger')
				->addColumn('sender_id', 'biginteger', ['null' => true])
				->addColumn('subject', 'string', ['limit' => 255])
				->addColumn('message_id', 'biginteger', ['null' => true])
				->addColumn('dt_read', 'datetime', ['null' => true])
				->addColumn('is_new', 'boolean', ['default' => 1])
				->addColumn('dt_archived', 'datetime', ['null' => true])
				->addColumn('is_archived', 'boolean', ['default' => 1])
				->addColumn('parent_message_id', 'biginteger', ['null' => true])
				->addColumn('has_parent', 'boolean', ['default' => 0])
				->addColumn('del_forever', 'boolean', ['default' => 0])
				->addCmfiveParameters(['creator_id', 'modifier_id', 'dt_modified'])
				->create();

		$this->table('inbox_message', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('message', 'text')
				->addColumn('digest', 'string', ['limit' => 255])
				->create();
	}

	public function down() {
		$this->dropTable('inbox');
		$this->dropTable('inbox_message');
	}

}
