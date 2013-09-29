<?php

class FormsFormField extends DbObject {
	
	public $title;
	public $slug;
	public $description;
	public $position;
	
	public $field_type; // section, input, textarea, select, checkbox, date, time, upload
	public static $_field_type_ui_select_strings = array("section", "input", "textarea", "select", "checkbox", "date", "time", "upload");
	
	public $data_type; // text, integer, float, date, time, file, url, money
	public static $_data_type_ui_select_strings = array("text", "integer", "float", "date", "time", "file", "url", "money");
	
	// specific to input/text fields
	public $width;
	public $height;
	
	// specific to date/time
	public $date_format;
	public $time_format;
	
	// specific to select fields
	public $select_values; // comma separated list of values for select
	public $select_form_id; // OR select data from another form
	public $select_form_field_ids; // optionally display the following fields in select box, comma separated
	
	// specific to uploads
	public $file_types; // if set allow only those, eg. "jpg,gif,png"
	public $file_max_size; // if set allow only files of this size
	
	// set a default value
	public $default_value;
	
	// Metadata
	public $dt_created;
	public $creator_id;
	public $dt_modified;
	public $modifier_id;
	
	public static $_db_table = "forms_form_field";
	
	// validation
	public $_validation = array(
			"field_type" => array("in" => array("section", "input", "textarea", "select", "checkbox", "date", "time", "upload")),
			"data_type" => array("in" => array("text", "integer", "float", "date", "time", "file", "url", "money"))
	);
		
	static function getFieldTypes() {
		return self::$_field_type_ui_select_strings;
	}
	
	static function getDataTypes() {
		return self::$_data_type_ui_select_strings;
	}
	
	function insert($force_validation = false) {
		$this->slug = toSlug($this->title);
		parent::insert();
	}
	
	
}
