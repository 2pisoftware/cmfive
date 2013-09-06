<?php

class FormsApplicationMember extends DbObject {
	
	public $application_id;
	public $user_id;
	public $role; // admin, creator, user
	
	// Metadata
	public $dt_created;
	public $creator_id;
	public $dt_modified;
	public $modifier_id;
	
	static function getRoles() {
		return explode(',',"Administrator,Creator,User");
	}
	
	function getDbTableName() {
		return "forms_application_member";
	}
}