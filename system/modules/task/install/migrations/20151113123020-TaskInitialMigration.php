<?php

class TaskInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		// Create task table
		$this->table('task', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('is_closed', 'boolean', ['default' => 0])
				->addColumn('parent_id', 'biginteger', ['null' => true])
				->addColumn('title', 'string', ['limit' => 255, 'null' => true])
				->addColumn('task_group_id', 'biginteger')
				->addColumn('status', 'string', ['limit' => 255])
				->addColumn('priority', 'string', ['limit' => 255, 'null' => true])
				->addColumn('task_type', 'string', ['limit' => 255, 'null' => true])
				->addColumn('assignee_id', 'biginteger')
				->addColumn('first_assignee_id', 'biginteger')
				->addColumn('dt_assigned', 'datetime')
				->addColumn('dt_first_assigned', 'datetime')
				->addColumn('dt_completed', 'datetime', ['null' => true])
				->addColumn('dt_planned', 'datetime', ['null' => true])
				->addColumn('dt_due', 'datetime', ['null' => true])
				->addColumn('estimate_hours', 'integer', ['null' => true])
				->addColumn('description', 'text')
				->addColumn('latitude', 'string', ['limit' => 20, 'null' => true])
				->addColumn('longitude', 'string', ['limit' => 20, 'null' => true])
				->addCmfiveParameters(['dt_created', 'dt_modified', 'creator_id', 'modifier_id'])
				->create();

		// Create task data table
		$this->table('task_data', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('task_id', 'biginteger')
				->addColumn('data_key', 'string', ['limit' => 100])
				->addColumn('value', 'string', ['limit' => 255])
				->create();

		// Create taskgroup table
		$this->table('task_group', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('title', 'string', ['limit' => 50])
				->addColumn('can_assign', 'string', ['limit' => 50])
				->addColumn('can_view', 'string', ['limit' => 50])
				->addColumn('can_create', 'string', ['limit' => 50])
				->addColumn('is_active', 'boolean')
				->addColumn('description', 'text')
				->addColumn('task_group_type', 'string', ['limit' => 50])
				->addColumn('default_assignee_id', 'biginteger')
				->addCmfiveParameters(['dt_created', 'dt_modified', 'creator_id', 'modifier_id'])
				->create();

		// Create taskgroup member table
		$this->table('task_group_member', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('task_group_id', 'biginteger')
				->addColumn('user_id', 'biginteger')
				->addColumn('role', 'string', ['limit' => 50])
				->addColumn('priority', 'integer')
				->addColumn('is_active', 'boolean')
				->create();

		// Create taskgroup notify table
		$this->table('task_group_notify', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('task_group_id', 'biginteger')
				->addColumn('role', 'string', ['limit' => 255, 'null' => true])
				->addColumn('type', 'string', ['limit' => 255, 'null' => true])
				->addColumn('value', 'boolean', ['default' => 0])
				->create();

		// Create taskgroup user notify table
		$this->table('task_group_user_notify', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('user_id', 'biginteger')
				->addColumn('task_group_id', 'biginteger')
				->addColumn('role', 'string', ['limit' => 255, 'null' => true])
				->addColumn('type', 'string', ['limit' => 255, 'null' => true])
				->addColumn('value', 'boolean', ['default' => 0])
				->addColumn('task_creation', 'boolean', ['default' => 0])
				->addColumn('task_details', 'boolean', ['default' => 0])
				->addColumn('task_comments', 'boolean', ['default' => 0])
				->addColumn('time_log', 'boolean', ['default' => 0])
				->addColumn('task_documents', 'boolean', ['default' => 0])
				->addColumn('task_pages', 'boolean', ['default' => 0])
				->create();

		// Create taskgroup notify table
		$this->table('task_object', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('task_id', 'biginteger')
				->addColumn('key', 'string', ['limit' => 255])
				->addColumn('table_name', 'string', ['limit' => 255])
				->addColumn('object_id', 'biginteger')
				->create();

		$this->table('task_time', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('task_id', 'biginteger')
				->addColumn('user_id', 'biginteger')
				->addColumn('dt_start', 'datetime')
				->addColumn('dt_end', 'datetime')
				->addColumn('comment_id', 'biginteger')
				->addColumn('time_type', 'string', ['limit' => 255, 'null' => true])
				->addColumn('is_suspect', 'boolean', ['default' => 0])
				->addCmfiveParameters(['dt_modified', 'modifier_id'])
				->create();

		// Create taskgroup user notify table
		$this->table('task_user_notify', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('user_id', 'biginteger')
				->addColumn('task_id', 'biginteger')
				->addColumn('task_creation', 'boolean', ['default' => 0])
				->addColumn('task_details', 'boolean', ['default' => 0])
				->addColumn('task_comments', 'boolean', ['default' => 0])
				->addColumn('time_log', 'boolean', ['default' => 0])
				->addColumn('task_documents', 'boolean', ['default' => 0])
				->addColumn('task_pages', 'boolean', ['default' => 0])
				->create();
	}

	public function down() {
		$this->dropTable("task");
		$this->dropTable("task_data");
		$this->dropTable("task_group");
		$this->dropTable("task_group_member");
		$this->dropTable("task_group_notify");
		$this->dropTable("task_group_user_notify");
		$this->dropTable("task_object");
		$this->dropTable("task_time");
		$this->dropTable("task_user_notify");
	}

}
