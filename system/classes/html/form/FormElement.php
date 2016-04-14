<?php namespace Html\Form;

class FormElement extends \Html\Element {
	
	public $label;
	
	/**
	 * Sets the printable label used by {@see Html::multiColForm()}
	 * 
	 * @param String $label
	 * @return 
	 */
	public function setLabel($label) {
		$this->label = $label;
		
		return $this;
	}
	
}
