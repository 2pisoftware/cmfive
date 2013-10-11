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
	
	public static $_db_table = "forms_form_member";
	
	static function getRoles() {
		return explode(',',"Designer,Editor,Reader");
	}
	
}