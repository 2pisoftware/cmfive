<?php
class RestSession extends DbObject {
	var $user_id;
	var $token;
	var $dt_created;
	var $dt_modified;
	
	function setUser(User $user) {
		if ($user) {
			$this->user_id = $user->id;
			$this->token = sha1($user->id.$user->getFullName().time());
		}
	}
	
	function getUser() {
		if ($this->user_id) {
			return $this->getObject("User", $this->user_id);
		}
	}
	
	function getDbTableName() {
		return "rest_session";
	}
	
}