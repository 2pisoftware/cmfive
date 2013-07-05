<?php

class FormsFormInstanceData extends DbObject {

	// belongs to
	var $form_field_id;
	var $form_instance_id;
	
	// data
	var $text_data;
	var $integer_data;
	var $float_data;
	var $date_data;
	var $time_data;
	var $file_data;
	
	// Meta Data
	var $dt_created;
	var $dt_modified;
	var $creator_id;
	var $modifier_id;
	
	function getFormInstance() {
		return $this->getObject("FormsFormInstance", $this->form_instance_id);
	}

	function getFormField() {
		return $this->getObject("FormsFormField", $this->form_field_id);
	}
	
	function getDbTableName() {
		return "forms_form_instance_data";
	}
	
	
}
