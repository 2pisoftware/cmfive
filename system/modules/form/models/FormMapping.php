<?php

class FormMapping extends DbObject {
	
	public $form_id;
	public $object;

	public function getForm() {
		return $this->getObject("Form", $this->form_id);
	}
	
}