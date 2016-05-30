<?php

class WebdavPropertyStorageMigration extends CmfiveMigration {


/*********************
CREATE TABLE propertystorage (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    path VARBINARY(1024) NOT NULL,
    name VARBINARY(100) NOT NULL,
    valuetype INT UNSIGNED,
    value MEDIUMBLOB
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE UNIQUE INDEX path_property ON propertystorage (path(600), name(100));
***************************/


	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);
		if (!$this->hasTable('propertystorage')) {
			$this->table('propertystorage', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('path', 'text', ['limit' => 1024])
					->addColumn('name', 'text', ['limit' => 100])
					->addColumn('valuetype', 'integer',[])
					->addColumn('value', 'text', [])
					->create();
		}
	}



	public function down() {
		$this->hasTable('propertystorage') ? $this->dropTable('propertystorage') : null;
		$this->hasTable('user_role') ? $this->dropTable('user_role') : null;
		$this->hasTable('group_user') ? $this->dropTable('group_user') : null;
		$this->hasTable('contact') ? $this->dropTable('contact') : null;
	}

}
