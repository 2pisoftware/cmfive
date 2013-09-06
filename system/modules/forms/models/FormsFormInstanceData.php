<?php

class FormsFormInstanceData extends DbObject {

	// belongs to
	public $form_field_id;
	public $form_instance_id;
	
	// data
	public $text_data;
	public $integer_data;
	public $float_data;
	public $date_data;
	public $time_data;
	public $file_data;
	
	// Meta Data
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
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
