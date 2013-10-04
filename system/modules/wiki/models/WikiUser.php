<?php
class WikiUser extends DbObject {
	public $wiki_id;
	public $user_id;
	public $role;
	
	function getUser() {
		return $this->Auth->getUser($this->user_id);
	}
	
	function getFullName() {
		return $this->getUser()->getFullName();	
	}
	
	function getDbTableName() {
		return "wiki_user";
	}
}