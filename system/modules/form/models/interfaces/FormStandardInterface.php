<?php

class FormStandardInterface extends FormFieldInterface {
	
	protected $_respondsTo = [
		"Text" => "text",
		"Date" => "date",
		"Date & Time" => "datetime"
	];
	
	public function modifyForDisplay($type, $value) {
		if (!$this->doesRespondTo($type)) {
			return $value;
		}
		
		// Alter value based on type
		switch (strtolower($type)) {
			case "date":
				return date("d/m/Y", $value);
			case "datetime":
				return date("d/m/Y H:i:s", $value);
			default:
				return $value;
		}
	}

	public function modifyForPersistance($type, $value) {
		if (!$this->doesRespondTo($type)) {
			return $value;
		}
		
		// Alter value based on type
		switch (strtolower($type)) {
			case "date":
			case "datetime":
				return strtotime(str_replace("/", "-", $value));
			default:
				return $value;
		}
	}

}
