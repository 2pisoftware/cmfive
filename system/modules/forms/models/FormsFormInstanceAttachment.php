<?php
class FormsFormInstanceAttachment extends DbObject {

	public $form_instance_id;
	public $file;

	// Meta Data
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;

	function getDbTableName() {
		return "forms_form_instance_attachment";
	}
}