<?php

class FormStandardInterface extends FormFieldInterface {
	
	protected static $_respondsTo = [
		["Text", "text"],
		["Decimal", "decimal"],
		["Date", "date"],
		["Date & Time", "datetime"]
	];
	
	public static function formType($type) {
		if (!static::doesRespondTo($type)) {
			return null;
		}
		
		switch(strtolower($type)) {
			case "date": 
				return "date"; 
			case "datetime":
				return "datetime";
			case "decimal":
			case "text":
			default:
				return "text";
		}
		return null;
	}
	
	public static function modifyForDisplay($type, $value) {
		if (!static::doesRespondTo($type)) {
			return $value;
		}
		
		// Alter value based on type
		switch (strtolower($type)) {
			case "decimal":
				return $value * 1.0;
			case "date":
				return date("d/m/Y", $value);
			case "datetime":
				return date("d/m/Y H:i:s", $value);
			default:
				return $value;
		}
	}

	public static function modifyForPersistance($type, $value) {
		if (!static::doesRespondTo($type)) {
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
