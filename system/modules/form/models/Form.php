<?php

class Form extends DbObject {
	
	public $title;
	public $description;
	
	public function getFields() {
		return $this->getObjects("FormField", ["form_id" => $this->id, "is_deleted" => 0]);
	}
	
	public function getSelectOptionTitle() {
		return $this->title;
	}
	
	public function getSelectOptionValue() {
		return $this->id;
	}
	
	public function printSearchTitle() {
		return $this->title;
	}
	
	public function printSearchUrl() {
		return "/form/show/" . $this->id;
	}

}