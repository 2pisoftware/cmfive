<?php
/**
 * ExampleData class for demonstration purposes
 * 
 * @author Carsten Eckelmann, May 2014
 */
class ExampleData extends DbObject {
	
	// object properties
	
	// public $id; <-- this is defined in the parent class
	public $title;
	public $data;
	//public $check;
	
	// standard system properties
	
	public $is_deleted; // <-- is_ = tinyint 0/1 for false/true
	public $dt_created; // <-- dt_ = datetime values
	public $dt_modified;
	public $modifier_id; // <-- foreign key to user table
	public $creator_id; // <-- foreign key to user table
	
	// this makes it searchable
	
	public $_searchable;
	
	// functions for how to behave when displayed in search results
	
	public function printSearchTitle() {
		return $this->title;
	}
	
	public function printSearchListing() {
		return $this->data;
	}
	
	public function printSearchUrl() {
		return "example/show/".$this->id;
	}		
	
	// functions for implementing access restrictions, these are optional

	public function canList(User $user) {
		return $user !== null && $user->hasAnyRole(array("example_admin"));
	}
	
	public function canView(User $user) {
		return $user !== null && $user->hasAnyRole(array("example_admin"));
	}
	
	public function canEdit(User $user) {
		return $user !== null && $user->hasAnyRole(array("example_admin"));
	}
	
	public function canDelete(User $user) {
		return $user !== null && $user->hasAnyRole(array("example_admin"));
	}	
	
	// functions for how to display inside a dropdown, these are optional
	
	public function getSelectOptionTitle() {
		return $this->title;
	}
	
	public function getSelectOptionValue() {
		return $this->id;
	}
	
	// override this function to add stuff to the search index
	// DO NOT CALL $this->getIndexContent() within this function
	// or you will create an endless loop which will destroy the universe!	

	function addToIndex() {
		return null;
	}	
	
	// you could override these functions, but only if you must, 
	// otherwise just delete them from this class
	
	public function update($force_nullvalues = false, $force_validation = false ) {
		parent::update($force_nullvalues, $force_validation);
	}

	public function insert($force_validation = false ) {
		parent::insert($force_validation);
	}
	
	public function delete($force = false ) {
		parent::delete($force);
	}
	
}