<?php
class Template extends DbObject {
	public $title;
	public $description;
	
	public $category; // eg. Invoice, Quote, Form Letter, Contract, etc.
	public $module; // which module to use this for
	
	public $is_active;
	
	public $template_title; // this can be used to automatically generated email subject lines
	public $template_body; // this contains the template body which contains replacement markers 
	
	// standard object stuff
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
	protected $_db_table = "template";
	
	public function renderTitle($data) {
		return $this->Template->render($this->title,$data);
	}

	public function renderBody($data) {
		return $this->Template->render($this->body,$data);
	}
}