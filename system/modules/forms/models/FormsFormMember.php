<?php
class FormsFormMember extends DbObject {

	public $form_id;
	public $user_id;
	public $role;
	
	// Metadata
	public $dt_created;
	public $creator_id;
	public $dt_modified;
	public $modifier_id;
	
	static function getRoles() {
		return explode(',',"Designer,Editor,Reader");
	}
	
	function getDbTableName() {
		return "forms_form_member";
	}
	
}