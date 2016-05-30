<?php

class AuthUserPasswordDigestMigration extends CmfiveMigration {

	public function up() {
		// UP
        if ($this->hasTable("user") && !$this->table("user")->hasColumn("password_digest")) {
            $this->table("user")->addColumn("password_digest", "string", ["limit" => 255, "null" => true])->save();
        }
	}

	public function down() {
		// DOWN
        $this->hasTable("user") && $this->table("user")->hasColumn("password_digest") ? $this->table("user")->removeColumn("password_digest") : null;
	}

}
