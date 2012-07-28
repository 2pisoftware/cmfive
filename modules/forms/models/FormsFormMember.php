<?php
class FormsFormMember extends DbObject {

	var $form_id;
	var $user_id;
	var $role;
	
	// Metadata
	var $dt_created;
	var $creator_id;
	var $dt_modified;
	var $modifier_id;
	
	static function getRoles() {
		return explode(',',"Designer,Editor,Reader");
	}
	
	function getDbTableName() {
		return "forms_form_member";
	}
	
}