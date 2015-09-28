<?php

class FormInstance extends DbObject {
	
	public $form_id;
	public $object_class;
	public $object_id;
	
	public function getForm() {
		return $this->getObject("Form", $this->form_id);
	}
	
	public function getSavedValues() {
		return $this->getObjects("FormValue", ["form_instance_id" => $this->id, "is_deleted" => 0]);
	}
	
	public function getTableRow() {
		$form_values = $this->getSavedValues();
		
		
	}
	
	public function getEditForm($form) {
		if (empty($form->id)) {
			$form = $this->getForm();
			if (empty($form->id)) {
				$form = new Form($this->w);
			}
		}
		
		$form_values = $this->getSavedValues();
		$form_structure = []; // $w->Form->buildForm($this);
		
		if (!empty($form_values)) {
			foreach($form_values as $value) {
				$form_structure[] = array($value->getFormRow());
			}
		} else {
			$form_fields = $form->getFields();
			if (!empty($form_fields)) {
				foreach($form_fields as $field) {
					$form_structure[] = array($field->getFormRow());
				}
			}
		}
		return array($form->title => $form_structure);
	}
}