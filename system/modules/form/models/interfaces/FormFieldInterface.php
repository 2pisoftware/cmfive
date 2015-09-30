<?php

/**
 * This abstract class is for defining a standard way that field types
 * can be created. The system can them look at all instances that implement
 * this interface and present them to the user. The advantage to this is that
 * modules can define their own form fields as long as it implements this
 * interface
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
abstract class FormFieldInterface {
	
	// The definition of what form types this class can manipulate
	// Format should be ["<NAME>" => "<DB VALUE>"] (note the types
	// defined here are persisted against the form object)
	protected $_respondsTo = [
		// "Money" => "money"
	];
	
	/**
	 * The list of types that the interface responds to
	 * This will be used to generate a listing of the available form
	 * fields, therefore they can be anything
	 * 
	 * @return Array
	 */
	public function respondsTo() {
		return $this->_respondsTo;
	}
	
	/**
	 * Returns whether or not this class can interact with a given type
	 * 
	 * @param String $type
	 * @return boolean
	 */
	public function doesRespondTo($type) {
		return in_array($type, $this->respondsTo());
	}
	
	/**
	 * This is where the 'magic' happens. Based on the given type, the class
	 * will modify output, the producer of these classes are entirely responsible
	 * for making sure the output here is capable of dealing with errors
	 * 
	 * The recommendation is to return the $value in the event of an error (like
	 * an unknown type)
	 * 
	 * @param String $type
	 * @param Mixed $value
	 * @return Mixed
	 */
	abstract function modifyForDisplay($type, $value);
	
	//	E.g. for error handling
	//	public function modifyForDisplay($type, $value) {
	//		if (!$this->doesRespondTo($type)) {
	//			return $value;
	//		}
	//		// Do something to $value
	//		return $value;
	//	}
	
	/**
	 * Much like the modifyForDisplay function, this function is for
	 * manipulating the value, this value is given by the user interface so
	 * will generally be a string, from here, you can modify it ready for
	 * persistance.
	 * 
	 * An example of these two functions at work would be storing a datetime
	 * value as a unix timestamp; in modifyForDisplay, you would convert $value
	 * from a unix timestamp to a date time string (e.g 'H:i d-m-Y' format) and
	 * in the modifyForPersistance function you would convert the string back to
	 * a unix timestamp using strtotime()
	 * 
	 * @see FormFieldInterface::modifyForDisplay()
	 * @param String $type
	 * @param Mixed $value
	 * @return Mixed
	 */
	abstract function modifyForPersistance($type, $value); 
	
}