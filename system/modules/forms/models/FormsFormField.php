<?php

class FormsFormField extends DbObject {
	
	public $title;
	public $slug;
	public $description;
	public $position;
	
	public $field_type; // section, input, textarea, select, checkbox, date, time, upload
	
	public $data_type; // text, integer, float, date, time, file, url, money
	
	// specific to input/text fields
	public $width;
	public $height;
	
	// specific to date/time
	public $date_format;
	public $time_format;
	
	// specific to select fields
	public $select_values; // comma separated list of values for select
	public $select_form_id; // OR select data from another form
	public $select_form_field_ids; // display the following fields in select box
	
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
	
	static function getFieldTypes() {
		return explode(',',"section,input,textarea,select,checkbox,date,time,upload");
	}
	
	static function getDataTypes() {
		return explode(',',"text,integer,float,date,time,file,url,money");
	}
	
	function insert() {
		$this->slug = toSlug($this->title);
		parent::insert();
	}
	
	function getDbTableName() {
		return "forms_form_field";
	}
	
}
