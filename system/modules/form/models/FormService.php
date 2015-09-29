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
	
	public function getFormInstance($id) {
		return $this->getObject("FormInstance", $id);
	}
	
	public function buildForm(FormInstance $form_instance, Form $form) {
		$form_structure = $form_instance->getEditForm($form);
		return $form_structure;
	}
	
	public function isFormMappedToObject($form, $object) {
		$mapping = $this->getObject("FormMapping", ["form_id" => $form->id, "object" => $object, "is_deleted" => 0]);
		return !empty($mapping->id);
	}

	public function areFormsMappedToObject($object) {
		$mapping = $this->getObjects("FormMapping", ["object" => get_class($object), "is_deleted" => 0]);
		return count($mapping) > 0;
	}
	
	public function getFormsMappedToObject($object) {
		$mapping = $this->getObjects("FormMapping", ["object" => get_class($object), "is_deleted" => 0]);
		$forms = [];
		if (!empty($mapping)) {
			foreach($mapping as $map) {
				$forms[] = $map->getForm();
			}
		}
		
		return $forms;
	}
	
	public function getFormFieldByFormIdAndTitle($form_id, $name) {
		return $this->getObject("FormField", ["form_id" => $form_id, "technical_name" => $name, "is_deleted" => 0]);
	}
}
