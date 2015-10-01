<?php

class FormField extends DbObject {
	
	public $form_id;
	public $name;
	public $technical_name;
	public $interface_class;
	public $type;
	public $mask;

	public function insert($force_validation = true) {
		$this->technical_name = strtolower(str_replace(" ", "_", $this->name));
		$this->setInterfaceClass();
		
		parent::insert($force_validation);
	}
	
	public function update($force_null_values = false, $force_validation = true) {
		$this->technical_name = strtolower(str_replace(" ", "_", $this->name));
		$this->setInterfaceClass();
		
		parent::update($force_null_values, $force_validation);
	}

	public function setInterfaceClass() {
		// Set interface class
		$interfaces = Config::get('form.interfaces');
		if (!empty($interfaces)) {
			foreach($interfaces as $interface) {
				if ($interface::doesRespondTo($this->type)) {
					$this->interface_class = $interface;
				}
			}
		}
	}
	
	public static function getFieldTypes() {
		$interfaces = Config::get('form.interfaces');
		$fieldTypes = [];
		if (!empty($interfaces)) {
			foreach($interfaces as $interface) {
				$fieldTypes += $interface::respondsTo();
			}
		}
		return $fieldTypes;
	}
	
	public function getFormReferenceName() {
		return str_replace(" ", "_", $this->name);
	}
	
	public function getFormRow() {
		if (empty($this->type)) {
			return null;
		}
		
		$interface = $this->interface_class;
		return [
			$this->name, $interface::formType($this->type), $this->technical_name
		];
	}
	
	public function getMetaDataForm() {
		
	}
}
