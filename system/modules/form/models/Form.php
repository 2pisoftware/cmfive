<?php

class Form extends DbObject {
	
	public $title;
	public $description;
	public $header_template;
	public $row_template;
	public $summary_template;
	
	public function getFields() {
		return $this->getObjects("FormField", ["form_id" => $this->id, "is_deleted" => 0]);
	}
	
	public function getTableHeaders() {
		$fields = $this->getFields();
		
		$header_string = '';
		if (!empty($fields)) {
			foreach($fields as $field) {
				$header_string .= '<th>' . $field->name . '</th>';
			}
		}
		
		return $header_string;
	}
	
	public function getFormInstances() {
		return $this->getObjects("FormInstance", ["form_id" => $this->id, "is_deleted" => 0]);
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