<?php

class ChannelsTestMigration extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("channel")) {
			$table = $this->table("channel");

			$table->addColumn("test", "string", ["null" => true, "limit" => 1234]);
			$table->save();
		}
	}

	public function down() {
		// DOWN
		if ($this->table("channel")->hasColumn("test")) {
			$this->table('channel')->removeColumn("test")->save();
		}
	}

}
