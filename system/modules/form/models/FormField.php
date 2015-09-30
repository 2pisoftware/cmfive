<?php

class FormField extends DbObject {
	
	public $form_id;
	public $name;
	public $technical_name;
	public $type;
	public $mask;
	
	public function insert($force_validation = true) {
		$this->technical_name = strtolower(str_replace(" ", "_", $this->name));
		parent::insert($force_validation);
	}
	
	public function update($force_null_values = false, $force_validation = true) {
		$this->technical_name = strtolower(str_replace(" ", "_", $this->name));
		parent::update($force_null_values, $force_validation);
	}

	public static function getFieldTypes() {
		return [
			["Whole Number", "number"],
			["Decimal", "decimal"],
			["Date", "date"],
			["DateTime", "datetime"],
			["Money", "money"],
			["Text", "text"],
		];
	}
	
	public function getFormReferenceName() {
		return str_replace(" ", "_", $this->name);
	}
	
	public function getFormRow() {
		if (empty($this->type)) {
			return null;
		}
		
		$field_type = null;
		switch(strtolower($this->type)) {
			case "date": 
				$field_type = "date"; 
				break;
			case "datetime":
				$field_type = "datetime";
				break;
			case "number": 
			case "decimal":
			case "money": 
			case "text":
			default:
				$field_type = "text";
				break;
		}
		return [
			$this->name, $field_type, $this->technical_name
		];
	}
	
	public function getMetaDataForm() {
		
	}
}
