<?php namespace Html\Form;

/**
 * Class representation of a select field - HTML5 only
 * 
 * Setter documentation provided from the Mozilla Developer Network 
 * <https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input>
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Select {
	
	use \Html\GlobalAttribtues;
	
	public $autofocus;
	public $disabled;
	public $form;
	public $multiple;
	public $name;
	public $required;
	public $size;
	
	/**
	 * This attribute lets you specify that a form control should have input
	 * focus when the page loads, unless the user overrides it, for example by
	 * typing in a different control. Only one form element in a document can
	 * have the autofocus attribute, which is a Boolean.
	 * 
	 * @param string $autofocus
	 * @return \Html\Form\Select this
	 */
	public function setAutofocus($autofocus) {
		$this->autofocus = $autofocus;
		
		return $this;
	}
	
	/**
	 * This Boolean attribute indicates that the user cannot interact with the
	 * control. If this attribute is not specified, the control inherits its
	 * setting from the containing element, for example fieldset; if there is no
	 * containing element with the disabled attribute set, then the control is
	 * enabled.
	 * 
	 * @param string $disabled
	 * @return \Html\Form\Select this
	 */
	public function setDisabled($disabled) {
		$this->disabled = $disabled;
		
		return $this;
	}
	
	/**
	 * The form element that the select element is associated with (its "form
	 * owner"). If this attribute is specified, its value must be the ID of a
	 * form element in the same document. This enables you to place select
	 * elements anywhere within a document, not just as descendants of their
	 * form elements.
	 * 
	 * @param string $form
	 * @return \Html\Form\Select this
	 */
	public function setForm($form) {
		$this->form = $form;
		
		return $this;
	}
	
	/**
	 * This Boolean attribute indicates that multiple options can be selected in
	 * the list. If it is not specified, then only one option can be selected at
	 * a time.
	 * 
	 * @param string $multiple
	 * @return \Html\Form\Select this
	 */
	public function setMultiple($multiple) {
		$this->multiple = $multiple;
		
		return $this;
	}
	
	/**
	 * The name of the control.
	 * 
	 * @param string $name
	 * @return \Html\Form\Select this
	 */
	public function setName($name) {
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * A Boolean attribute indicating that an option with a non-empty string
	 * value must be selected.
	 * 
	 * @param string $required
	 * @return \Html\Form\Select this
	 */
	public function setRequired($required) {
		$this->required = $required;
		
		return $this;
	}
	
	/**
	 * If the control is presented as a scrolled list box, this attribute
	 * represents the number of rows in the list that should be visible at one
	 * time. Browsers are not required to present a select element as a scrolled
	 * list box. The default value is 0.
	 * 
	 * @param string $size
	 * @return \Html\Form\Select this
	 */
	public function setSize($size) {
		$this->size = $size;
		
		return $this;
	}
	
}