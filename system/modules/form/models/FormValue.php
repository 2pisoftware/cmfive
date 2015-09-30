<?php

class FormValue extends DbObject {
	
	public $form_instance_id;
	public $form_field_id;
	public $value;
	public $type;
	public $mask;

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
		if (empty($this->type)) {
			return null;
		}
		
		switch($this->type) {
			case "date": 
				return formatDate($this->value);
			case "datetime":
				return formatDateTime($this->value);
			case "number": 
				return intval($this->value);
			case "decimal":
				return round($this->value, 2);
			case "money":
				return formatMoney("%.2n", $this->value);
			case "text":
			default:
				return $this->value;
		}
	}
}
