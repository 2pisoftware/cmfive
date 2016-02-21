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
		if (!empty($this->header_template)) {
			return $this->header_template;
		}
		
		$fields = $this->getFields();
		
		$header_string = '';
		if (!empty($fields)) {
			foreach($fields as $field) {
				$header_string .= '<th>' . $field->name . '</th>';
			}
		}
		
		return $header_string;
	}
	
	public function getSummaryRow() {
		if (!empty($this->summary_template)) {
			$instances = $this->getFormInstances();
			
			// Generate a more accessible structure of the form instances and its data
			$structure = [];
			if (!empty($instances)) {
				foreach($instances as $instance) {
					$saved_values = $instance->getSavedValues();
					
					if(!empty($saved_values)) {
						$instance_structure = [];
						foreach($saved_values as $saved_value) {
							$field = $saved_value->getFormField();
							$instance_structure[$field->technical_name] = $saved_value->value;
						}
					}
					
					$structure[] = $instance_structure;
				}
			}

			return $this->w->Template->render($this->summary_template, ["form" => $structure]);
		}
		return '';
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