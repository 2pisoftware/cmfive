<?php

class FormsForm extends DbObject {
	
	public $title;
	public $slug;	
	public $description;
	public $application_id;
	
	// Meta Data
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;

	/**
	 * 
	 * return all fields for this form
	 */
	function getFields() {
		$this->getObjects("FormsFormField",array("form_id",$this->id));
	}
	
	/**
	 * 
	 * return the application where this forms belongs to
	 * @return FormsApplication
	 */
	function getApplication() {
		return $this->getObject("FormsApplication", $this->application_id);
	}
	
	function getInstances($include_deleted=false) {
		$where['form_id']=$this->id;
		
		if (!$include_deleted) {
			$where['is_deleted'] = "0";
		}
		
		return $this->getObjects("FormsFormInstance",$where);
	}
	
	function delete($force) {
		// check if there's data in any of the forms
		// then decide what to do with it before
		// deleting it
		parent::delete($force);
	}
	
	function insert() {
		$this->slug = toSlug($this->title);
		parent::insert();
	}
	
	function getDbTableName() {
		return "forms_form";
	}
}