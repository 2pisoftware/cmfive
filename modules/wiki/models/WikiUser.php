<?php
class WikiUser extends DbObject {
	var $wiki_id;
	var $user_id;
	var $role;
	
	function & getUser() {
		return $this->auth->getUser($this->user_id);
	}
	
	function getFullName() {
		return $this->getUser()->getFullName();	
	}
	
	function getDbTableName() {
		return "wiki_user";
	}
}