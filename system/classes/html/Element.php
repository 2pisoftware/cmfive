<?php namespace Html;

/**
 * The base class for a Cmfive HTML element, this class is intended to house
 * commonly required functionality between multiple HTML elements
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
abstract class Element {
	
	public $label;
	
	/**
	 * Constructor to set fields for subclassed object
	 * 
	 * @param Array $fields
	 */
	public function __construct($fields = []) {
		if (!is_null($fields) && is_array($fields) && count($fields) > 0) {
			foreach($fields as $key => $value) {
				if (property_exists($this, $key)) {
					$this->{$key} = $value;
				}
			}
		}
	}

	/**
	 * Sets the label for Cmfive used by form builders
	 * @param string $label
	 * @return \Html\Element this
	 */
	public function setLabel($label) {
		$this->label = $label;
		
		return $this;
	}
	
}