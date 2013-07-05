<?php

class FormsApplicationMember extends DbObject {
	
	var $application_id;
	var $user_id;
	var $role; // admin, creator, user
	
	// Metadata
	var $dt_created;
	var $creator_id;
	var $dt_modified;
	var $modifier_id;
	
	static function getRoles() {
		return explode(',',"Administrator,Creator,User");
	}
	
	function getDbTableName() {
		return "forms_application_member";
	}
}