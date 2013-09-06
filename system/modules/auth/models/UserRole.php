<?php
class UserRole extends DbObject {
	public $user_id;
	public $role;
	function getDbTableName() {
		return "user_role";
	}
}
