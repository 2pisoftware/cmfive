<?php
class FormsFormInstanceComment extends DbObject {
	
	public $form_instance_id;
	public $comment;
	
	// Meta Data
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
	function getDbTableName() {
		return "forms_form_instance_comment";
	}
}