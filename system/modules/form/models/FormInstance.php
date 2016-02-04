<?php

class FormInstance extends DbObject {
	
	public $form_id;
	public $object_class;
	public $object_id;
	
	public function getLinkedObject() {
		return $this->getObject($this->object_class, $this->object_id);
	}
	
	public function getForm() {
		return $this->getObject("Form", $this->form_id);
	}
	
	public function getSavedValues() {
		return $this->getObjects("FormValue", ["form_instance_id" => $this->id, "is_deleted" => 0]);
	}
	
	public function getTableRow() {
		$form_values = $this->getSavedValues();
		
		$table_row = '';
		if (!empty($form_values)) {
			foreach($form_values as $form_value) {
				$table_row .= "<td>" . $form_value->getMaskedValue() . "</td>";
			}
		}
		return $table_row;
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
	
	/**
	 * The following can* functions a overridden to implement the linked
	 * objects own matching functions.
	 * 
	 * The use case is, for example, if a user can view a Task but not edit
	 * then those permissions are reflect in the attached form data
	 */
	public function canList(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canList($user);
		}
		
		return parent::canList($user);
	}
	
	public function canView(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canView($user);
		}
		
		return parent::canView($user);
	}
	
	public function canEdit(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canEdit($user);
		}
		
		return parent::canEdit($user);
	}
	
	public function canDelete(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canDelete($user);
		}
		
		return parent::canDelete($user);
	}
}