<?php

class FormValue extends DbObject {
	
	public $form_instance_id;
	public $form_field_id;
	public $value;
	public $field_type;
	public $mask;

	public function insert($force_validation = true) {
		$field = $this->getFormField();
		
		$interface = $field->interface_class;
		$this->value = $interface::modifyForPersistance($field->type, $this->value);
		
		parent::insert($force_validation);
	}
	
	public function update($force_null_values = false, $force_validation = true) {
		$field = $this->getFormField();
		
		$interface = $field->interface_class;
		$this->value = $interface::modifyForPersistance($field->type, $this->value);
		
		parent::update($force_validation);
	}
	
	public function getFieldName() {
		$field = $this->getFormField();
		return $field->name;
	}
	
	public function getFormField() {
		return $this->getObject("FormField", $this->form_field_id);
	}
	
	public function getFormRow() {
		$field = $this->getFormField();
		$row = $field->getFormRow();
		$value = $this->getMaskedValue();
		
		array_push($row, $value);
		return $row;
	}
	
	public function getMaskedValue() {
		if (empty($this->field_type)) {
			return null;
		}
		
		$field = $this->getFormField();
		$interface = $field->interface_class;
		
		return $interface::modifyForDisplay($this->field_type, $this->value);
//		
//		switch($this->type) {
//			case "date": 
//				return formatDate($this->value);
//			case "datetime":
//				return formatDateTime($this->value);
//			case "number": 
//				return intval($this->value);
//			case "decimal":
//				return round($this->value, 2);
//			case "money":
//				return formatMoney("%.2n", $this->value);
//			case "text":
//			default:
//				return $this->value;
//		}
	}
}
