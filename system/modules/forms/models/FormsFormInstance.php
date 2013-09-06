<?php

class FormsFormInstance extends DbObject {
	
	public $form_id;
	
	// Meta Data
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
	function getData() {
		return $this->getObjects("FormsFormInstanceData",array("form_instance_id",$this->id));	
	}
	
	function getComments() {
		return $this->getObjects("FormsFormInstanceComment",array("form_instance_id",$this->id));
	}
	
	function getAttachments() {
		return $this->File->getAttachments($this);
	}
	
	function getDbTableName() {
		return "forms_form_instance";
	}
}
