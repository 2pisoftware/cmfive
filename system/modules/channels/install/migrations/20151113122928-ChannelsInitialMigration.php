<?php

class ChannelsInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		// Create channel table
		$this->table('channel', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('is_active', 'boolean', ['default' => 1])
				->addColumn('name', 'string', ['limit' => 255, 'null' => true])
				->addColumn('notify_user_email', 'string', ['limit' => 255, 'null' => true])
				->addColumn('notify_user_id', 'biginteger', ['null' => true])
				->addColumn('do_processing', 'boolean', ['default' => 1])
				->addCmfiveParameters()
				->create();
		
		// Create channel email option table
		$this->table('channel_email_option', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('channel_id', 'biginteger')
				->addColumn('server', 'string', ['limit' => 1024])
				->addColumn('s_username', 'string', ['limit' => 512, 'null' => true])
				->addColumn('s_password', 'string', ['limit' => 512, 'null' => true])
				->addColumn('port', 'integer', ['limit' => 11, 'null' => true])
				->addColumn('use_auth', 'boolean', ['default' => 1])
				->addColumn('folder', 'string', ['limit' => 255, 'null' => true])
				->addColumn('protocol', 'string', ['limit' => 255, 'null' => true])
				->addColumn('to_filter', 'string', ['limit' => 255, 'null' => true])
				->addColumn('from_filter', 'string', ['limit' => 255, 'null' => true])
				->addColumn('subject_filter', 'string', ['limit' => 255, 'null' => true])
				->addColumn('cc_filter', 'string', ['limit' => 255, 'null' => true])
				->addColumn('body_filter', 'string', ['limit' => 255, 'null' => true])
				->addColumn('post_read_action', 'string', ['limit' => 255, 'null' => true])
				->addColumn('post_read_parameter', 'string', ['limit' => 255, 'null' => true])
				->addCmfiveParameters()
				->create();
		
		// Create channel message table
		$this->table('channel_message', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('channel_id', 'biginteger')
				->addColumn('message_type', 'string', ['limit' => 255])
				->addColumn('is_processed', 'boolean', ['default' => 1])
				->addCmfiveParameters()
				->create();
		
		// Create channel message table
		$this->table('channel_message_status', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('message_id', 'biginteger')
				->addColumn('processor_id', 'biginteger')
				->addColumn('message', 'text')
				->addColumn('is_successful', 'boolean', ['default' => 0])
				->addCmfiveParameters()
				->create();
		
		// Create channel message table
		$this->table('channel_processor', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('channel_id', 'biginteger')
				->addColumn('class', 'string', ['limit' => 255])
				->addColumn('module', 'string', ['limit' => 255])
				->addColumn('name', 'string', ['limit' => 255, 'null' => true])
				->addColumn('processor_settings', 'string', ['limit' => 1024, 'null' => true])
				->addColumn('settings', 'string', ['limit' => 1024, 'null' => true])
				->addCmfiveParameters()
				->create();
	}

	public function down() {
		$this->dropTable("channel");
		$this->dropTable("channel_email_option");
		$this->dropTable("channel_message");
		$this->dropTable("channel_message_status");
		$this->dropTable("channel_processor");
	}

}
