<?php
/**
 * Class FormsApplication:
 * 
 * An Application is a collection of Form(Definitions) and Members who have
 * certain access rights on these forms, e.g. Create new Form, Edit existing, 
 * Delete existin, etc.
 * 
 */
class FormsApplication extends DbObject {
	
	public $title;
	public $slug;	
	public $description;
	
	// Meta Data
	public $is_deleted;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
	public static $_db_table = "forms_application";
	
	/**
	 * 
	 * return all forms for this application
	 * 
	 * @param bool $include_deleted
	 */
	function getForms($include_deleted=false) {
		$where['application_id']=$this->id;
		
		if (!$include_deleted) {
			$where['is_deleted'] = "0";
		}
		
		return $this->getObjects("FormsForm",$where);
	}
	
	function getForm($id) {
		return $this->getObject("FormsForm", $id);
	}
	/**
	 * 
	 * return all FormsApplicationMember objects for this application
	 */
	function getMembers() {
		return $this->getObjects("FormsApplicationMember",array("application_id",$this->id));
	}
	
	/**
	*
	* return all forms for which the user has access
	* 
	* @param integer $user_id
	*/
	function getFormsForUser($user_id) {
	
	}

	function insert($force_validation = false) {
		$this->slug = toSlug($this->title);
		parent::insert();
	}
	
	function delete($force = false) {
		// check if there's data in any of the forms
		// then decide what to do with it before
		// deleting it
		parent::delete($force);
	}
	
}