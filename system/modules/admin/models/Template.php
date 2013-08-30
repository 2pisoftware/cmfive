<?php
class Template extends DbObject {
	public $title;
	public $description;
	
	public $category; // eg. Invoice, Quote, Form Letter, Contract, etc.
	public $module; // which module to use this for, eg. crm
	
	public $is_active;
	
	public $template_title; // this can be used to automatically generated email subject lines
	public $template_body; // this contains the template body which contains replacement markers 
	public $test_title_json; // this can contain test data in JSON format for testing the template
	public $test_body_json; // this can contain test data in JSON format for testing the template
	
	// standard object stuff
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
	protected $_db_table = "template";
	
	public function renderTitle($data) {
		return $this->Template->render($this->template_title,$data);
	}

	public function renderBody($data) {
		return $this->Template->render($this->template_body,$data);
	}
	
	public function testTitle() {
		return $this->Template->render($this->template_title,$this->test_title_json);
	}

	public function testBody() {
		return $this->Template->render($this->template_body,$this->test_body_json);
	}
}