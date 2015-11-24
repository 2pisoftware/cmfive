<?php
/**
 *  SavedSearch class for flagging records
 * 
 * @author Robert Lockerbie, robert@lockerbie.id.au 2015
 */
class SavedSearch extends DbObject {
	
	// object properties
	
	public $user_id;
	public $term;
	public $limit_to;
	public $tags;
	public $object_class;
	public $object_id;
	
	// standard system properties
	
	public $is_deleted; // <-- is_ = tinyint 0/1 for false/true
	public $dt_created; // <-- dt_ = datetime values
	public $dt_modified;
	public $modifier_id; // <-- foreign key to user table
	public $creator_id; // <-- foreign key to user table
}