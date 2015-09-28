<?php

class FormService extends DbService {
	
	public function getForms() {
		return $this->getObjects("Form", ["is_deleted" => 0]);
	}
	
	public function getForm($id) {
		return $this->getObject("Form", $id);
	}
	
	public function getFormField($id) {
		return $this->getObject("FormField", $id);
	}
	
	public function buildForm(FormInstance $form_instance, Form $form) {
		$form_structure = $form_instance->getEditForm($form);
		return $form_structure;
	}
	
	public function isFormMappedToObject($form, $object) {
		$mapping = $this->getObject("FormMapping", ["form_id" => $form->id, "object" => $object, "is_deleted" => 0]);
		return !empty($mapping->id);
	}
	
}
