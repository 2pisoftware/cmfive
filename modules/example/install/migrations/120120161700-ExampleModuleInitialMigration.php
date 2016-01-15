<?php

class ExampleModuleInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * testmodule_data TABLE
		 */
		if (!$this->hasTable('example_data')) {
			$this->table('example_data', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('title', 'string', ['limit' => 255])
					->addColumn('data', 'string', ['limit' => 1024])
					->addColumn('example_checkbox', 'integer' )
					->addColumn('select_field', 'string', ['limit' => 255])
					->addColumn('autocomplete_field', 'string', ['limit' => 255])
					->addColumn('multiselect_field', 'string', ['limit' => 255])
					->addColumn('radio_field', 'string', ['limit' => 255])
					->addColumn('password_field', 'string', ['limit' => 255])
					->addColumn('email_field', 'string', ['limit' => 255])
					->addColumn('hidden_field', 'string', ['limit' => 255])
					->addColumn('d_date_field', 'date')
					->addColumn('t_time_field', 'time')
					->addColumn('dt_datetime_field', 'datetime')
					->addColumn('rte_field', 'string', ['limit' => 255])
					->addColumn('file_field', 'string', ['limit' => 255])
					->addColumn('multifile_field', 'string', ['limit' => 255])
					->addCmfiveParameters()
					->create();
		}
  }

	public function down() {
		$this->hasTable('example_data') ? $this->dropTable('example_data') : null;
		
	}

}
