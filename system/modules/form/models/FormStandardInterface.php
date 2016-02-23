<?php
/************************************************
 * This class provides a base implementation of FormFieldInterface.
 * The logic for rendering and processing of field types is handled here.
 * Currently only date, datetime, text and decimal field types are supported.
 ************************************************/
class FormStandardInterface extends FormFieldInterface {
	
	protected static $_respondsTo = [
		["Text", "text"],
		["Decimal", "decimal"],
		["Date", "date"],
		["Date & Time", "datetime"],
		["Auto Complete", "autocomplete"]
	];
	
	/************************************************
	 * Map FormField type to Html::multiColForm() type
	 * @return string
	 ************************************************/
	public static function formType($type) {
		if (!static::doesRespondTo($type)) {
			return null;
		}
		
		switch(strtolower($type)) {
			case "date": 
				return "date"; 
			case "datetime":
				return "datetime";
			case "autocomplete":
				return "autocomplete";
			case "decimal":
			case "text":
			default:
				return "text";
		}
		return null;
	}
	
	/************************************************
	 * Map Form metadata to an array of extra parameters to Html::multiColForm() 
	 * 
	 * @return []
	 ************************************************/
	public static function formConfig($type,$metaData,$w) {
		//print_r([$type,$metaData]);
		$options=[];
		if ($type=="autocomplete")  {
			if (!empty($metaData['object_type'])) {
				try {
					$service = new DbService($w);
					$objects=$service->getObjects($metaData['object_type']);
					foreach ($objects as $option) {
						$options[]=$option->getSelectOptionTitle();
					}
				} catch (Exception $e) {
					//silently fail no options
				}
			} else if (!empty($metaData['options'])) {
				$options=explode(",",$metaData['options']);
			}
		}
		return [$options];
	}
	
	/************************************************
	 * Provide form row definition array for metadata associated with 
	 * this type
	 * @return [[$name,$type,$field]]
	 ************************************************/
	public static function metadataForm($type) {
		if (!static::doesRespondTo($type)) {
			return null;
		}
		
		switch(strtolower($type)) {
			case "decimal":
				return [["Decimal Places", "text", "decimal_places"]];
			case "autocomplete":
				return [["Object", "text", "object_type"],["Filter", "text", "object_filter"],["Options", "text", "options"]];
			default:
				return null;
		}
	}
	
	/************************************************
	 * Transform a value into a format useful for presentation based on its type.
	 * Decimal types are rounded.
	 * Date types are presented in Australian date format.
	 * @return string
	 ************************************************/
	public static function modifyForDisplay($type, $value, $metadata = null) {
		if (!static::doesRespondTo($type)) {
			return $value;
		}
		
		// Alter value based on type
		switch (strtolower($type)) {
			case "decimal":
				$decimal_places = self::getMetadataForKey($metadata, "decimal_places");
				if (!empty($decimal_places->id)) {
					return round($value, $decimal_places->meta_value);
				} else {
					return $value * 1.0;
				}
			case "date":
				return date("d/m/Y", $value);
			case "datetime":
				return date("d/m/Y H:i:s", $value);
			default:
				return $value;
		}
	}

	/************************************************
	 * Transform date values into a format useful for DbObject based
	 * persistence.
	 * @return string
	 ************************************************/
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