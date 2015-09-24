<?php

class Form extends DbObject {
	
	public $title;
	public $description;
	
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
		return "/form/edit/" . $this->id;
	}
}