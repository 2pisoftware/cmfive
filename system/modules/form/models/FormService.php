<?php

class FormService extends DbService {
	
	public function getForms() {
		return $this->getObjects("Form", ["is_deleted" => 0]);
	}
	
	public function getForm($id) {
		return $this->getObject("Form", $id);
	}
	
}

