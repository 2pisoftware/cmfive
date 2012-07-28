<?php
class FormsFormInstanceAttachment extends DbObject {

	var $form_instance_id;
	var $file;

	// Meta Data
	var $is_deleted;
	var $dt_created;
	var $dt_modified;
	var $creator_id;
	var $modifier_id;

	function getDbTableName() {
		return "forms_form_instance_attachment";
	}
}