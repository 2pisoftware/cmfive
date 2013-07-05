<?php

class FormsFormInstance extends DbObject {
	
	var $form_id;
	
	// Meta Data
	var $is_deleted;
	var $dt_created;
	var $dt_modified;
	var $creator_id;
	var $modifier_id;
	
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
