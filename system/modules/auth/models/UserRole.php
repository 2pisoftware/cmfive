<?php
class UserRole extends DbObject {
	var $user_id;
	var $role;
	function getDbTableName() {
		return "user_role";
	}
}
