<?php

class FormsFormField extends DbObject {
	
	var $title;
	var $slug;
	var $description;
	var $position;
	
	var $field_type; // section, input, textarea, select, checkbox, date, time, upload
	
	var $data_type; // text, integer, float, date, time, file, url, money
	
	// specific to input/text fields
	var $width;
	var $height;
	
	// specific to date/time
	var $date_format;
	var $time_format;
	
	// specific to select fields
	var $select_values; // comma separated list of values for select
	var $select_form_id; // OR select data from another form
	var $select_form_field_ids; // display the following fields in select box
	
	// specific to uploads
	var $file_types; // if set allow only those, eg. "jpg,gif,png"
	var $file_max_size; // if set allow only files of this size
	
	// set a default value
	var $default_value;
	
	// Metadata
	var $dt_created;
	var $creator_id;
	var $dt_modified;
	var $modifier_id;
	
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
