<?php

class FormField extends DbObject {
	
	public $form_id;
	public $name;
	public $type;
	public $mask;
	
	public static function getFieldTypes() {
		return [
			"number" => "Whole Number",
			"decimal" => "Decimal",
			"date" => "Date",
			"datetime" => "DateTime",
			"money" => "Money",
			"text" => "Text",
		];
	}
	
	public function getMasksForFieldType() {
		if (empty($this->type)) {
			return null;
		}
		
		switch($this->type) {
			case "number": {
				return;
			}
			case "decimal": {
				return [
					"Decimal Places", "text", "decimal_places", $this->getMaskValue("decimal_places")
				];
			}
			case "date": {
				
				break;
			}
			case "datetime": {
				
				break;
			}
			case "money": {
				
				break;
			}
			case "text": {
				
				break;
			}
		}
	}
	
}
