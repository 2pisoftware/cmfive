<?php
/**
 * Magic Database object Subclasses should either
 * have the same name as the DB table, or need to
 * override getDbTableName() function.
 *
 * All properties need to have the same name as
 * DB table properties or need to be considered
 * in getDbColumnName() function.
 *
 * A subclass can define the following special
 * properties for special handling:
 *
 * 1. _* properties are considered transient not automatically
 *    saved to DB
 *
 * 2. dt_* any property starting with dt_ will be transformed
 *    into seconds when loaded from DB and turned back into
 *    MySQL datetime format when saved to DB.
 *
 * 3. d_* as above but data only
 *
 * 4. t_* as aboce but time only
 *
 * 5. is_deleted when exists will be set to 1 instead of
 *    deleting the table data on object::delete(), to really
 *    delete the data in the DB ::delete(true) must be called!
 *
 * 6. title when exists will be automatically used for
 *    object::getSelectOptionTitle() method
 *
 * 7. name when exists and title doesn't exist then will be used
 *    for object::getSelectOptionTitle() method
 *
 * 8. Automagic Select UI Field Hints, see getSelectOptions() for more info.
 *    static $_<fieldname>_ui_select_string = array("option1","option2",...);
 *    static $_<fieldname>_ui_select_lookup_code = "states";
 *    static $_<fieldname>_ui_select_objects_class = "Contact";
 *    static $_<fieldname>_ui_select_objects_filter = array("is_deleted"=>0);
 *
 * 9. Define the Database Table Name (optional, see DbObject::getDbTableName()):
 *    var $_db_table = "<table name>";
 *
 * DbObject supports the use of the following 'Aspects' which can be
 * added to any object using a magic '$_<aspect>' property:
 *
 * 1. ModifiableAspect -> var $_modifiable;
 *    This Aspect adds the following functions to a DbObject:
 *    a) $this->_modifiable->getCreator(), returns the user object of the creator
 *    b) $this->_modifiable->getModifier(), returns user object for last modifier
 *    c) $this->_modifiable->getCreatedDate(), returns timestamp of creation
 *    d) $this->_modifiable->getModifiedDate(), returns timestamp of last modification
 *
 *    using this aspect all insert() and update() calls will set these
 *    above properties automatically.
 *
 * 2. VersionableAspect -> var $_versionable;
 *    This Aspect addes the following function to a DbObject:
 *    a) $this->_versionable->getAllVersions(), returns all previous versions for this object
 *    b) $this->_versionable->getVersion($id), returns a specific version
 *    c) $this->_versionable->getLastVersion(), returns the latest version
 *
 *    Using this aspect all insert() and update() calls will cause a
 *    version record to be created with the current values, thus
 *    resulting in a list of previous object states.
 *
 *    Also all version objects have the modifiable aspect (see above).
 *
 * 3. SearchableAspect -> var $_searchable;
 *    This Aspect does not add any public functions to the object, but extends
 *    the insert/update/delete behaviour so that an index record is created (or updated)
 *    in the table object_index which contains the object_id reference and a sanitised 
 *    string of the content of the source object's fields for fulltext retrieval.
 *    
 *    Per default all properties (except thos in the $_exclude_index array) are concatenated
 *    and included in the index. In order to add custom content (eg. from dependent tables)
 *    create the following:
 *    
 *    function addToIndex() {}
 *    
 *    Which should return a string to be added to the indexable content. All sanitising and
 *    word de-duplication is performed on this.
 *    
 * 4. Aspects can be removed in the case of class inheritance. If the parent class has 
 *    var $_searchable; defined then this can be removed by a child class using:
 *    var $_remove_searchable. However further childclasses can no longer add this aspect!
 *    
 * 5. Auditing of inserts and updates happens automatically to an audit table.
 *    However this can be turned off by setting
 *    var $__use_auditing = false;
 *
 * @author carsten
 *
 */
class DbObject extends DbService {
	var $id;

	var $__password = 'j09aBcDeFFh24122'; // for encrypted fields change this!!

	/**
	 * Overrride this variable to false to turn off
	 * DB auditing for this table
	 *
	 * @var unknown_type
	 */
	var $__use_auditing = true;


	//Define this property if you want to use the
	//ModifiableAspect
	//  var $_modifiable;
	//
	//To remove this from child classes:
	//  var $_remove_modifiable;

	//Define this property if you want to use the
	//VersionableAspect
	//  var $_versionable;
	//
	//To remove this from child classes:
	//  var $_remove_versionable;
	
	//Define this property if you want to use the
	//SearchableAspect
	//  var $_searchable;
	//
	//To remove this from child classes:
	//  var $_remove_searchable;
	
	
	/**
	 * Constructor
	 *
	 * @param $w
	 */
	function __construct(Web &$w) {
		parent::__construct($w);
		 
		// add standard aspects
		if (property_exists($this,"_modifiable") && !property_exists($this,"_remove_modifiable")) {
			$this->_modifiable = new AspectModifiable($this);
		}
		if (property_exists($this,"_versionable") && !property_exists($this,"_remove_versionable")) {
			$this->_versionable = new AspectVersionable($this);
		}
		if (property_exists($this,"_searchable") && !property_exists($this,"_remove_searchable")) {
			$this->_searchable = new AspectSearchable($this);
		}
	}

	public function __get($name) {
		// cater for modifiable aspect!
		if ($this->_modifiable) {
			if ($name == "dt_created") {
				return $this->_modifiable->getCreatedDate();
			}
			if ($name == "dt_modified") {
				return $this->_modifiable->getModifiedDate();
			}
		}
		return $this->w->$name;
	}

	/**
	 * Set a cryptography password for
	 * automatic encryption, decryption
	 *
	 * for 128bit AES choose 16 characters
	 * for 192bit AES choose 24 characters
	 * for 256bit AES choose 32 characters
	 */
	function setPassword($password) {
		if ($password) {
			$this->__password = $password;
		}
	}

	/**
	 * decrypt all fields that are marked with
	 * a 's_' prefix
	 */
	function decrypt() {
		foreach (get_object_vars($this) as $k => $v) {
			if (strpos($k,"s_") === 0) {
				if ($v) {
					$this->$k = decrypt($v,$this->__password);
				}
			}
		}
	}

	/**
	 *
	 * intermediate method to facilitate transition from
	 * selectTitle to getSelectOptionTitle
	 */
	function _selectOptionTitle() {
		$title = $this->getSelectOptionValue();
		if (property_exists(get_class($this), "title")) {
			$title = $this->title;
		} else if (property_exists(get_class($this), "name")) {
			$title = $this->name;
		}
		return $title;
	}
	/**
	 * is used by the Html::select() function to display this object in
	 * a select list. Could also be used by other similar functions.
	 */
	function getSelectOptionTitle() {
		return $this->_selectOptionTitle(); // only until all references are resolved
	}

	/**
	 * This is used by the Html::select() function to retrieve the key/title pairing
	 *
	 * this should only be overridden, if the id is NOT the key.
	 */
	function getSelectOptionValue() {
		return $this->id;
	}
	/**
	 * used by the search display function to print a title with a
	 * possible link for this item in the list of results.
	 */
	function printSearchTitle() {
		return get_class($this)."[".$this->id."]";
	}

	/**
	 * used by the search display function to print more information
	 * about this item in the list of search results.
	 */
	function printSearchListing() {
		return get_class($this)."[".$this->id."]";
	}

	/**
	 * used by the search display function to print a url for viewing details
	 * about this item.
	 */
	function printSearchUrl() {
		return null;
	}

	/**
	 * used by the search display function to check whether the user has
	 * permission to see this result item.
	 *
	 * @param <type> $user
	 * @return <type>
	 */
	function canList(User $user) {
		return true;
	}

	/**
	 * used by the search display function to check whether the user has
	 * permission to view further details about this item.
	 *
	 * @param User $user
	 */
	function canView(User $user) {
		return true;
	}

	/**
	 * can be used by other function to check whether the user has
	 * permissions to edit this item.
	 *
	 * @param User $user
	 */
	function canEdit(User $user) {
		return true;
	}

	/**
	 * can be used by other functions to check whether this user has
	 * permissions to delete this item.
	 *
	 * @param User $user
	 */
	function canDelete(User $user) {
		return true;
	}

	/**
	 * fill this object from an array where the keys correspond to the
	 * variable of this object.
	 *
	 * @param array $row
	 */
	function fill(& $row, $from_db = false) {
		foreach (get_object_vars($this) as $k => $v) {
			if ($k{0} != "_") { // ignore volatile vars
				$dbk = $k;
				if ($from_db) {
					$dbk = $this->getDbColumnName($k);
				}
				if (array_key_exists($dbk, $row) ) {
					$v = $row[$dbk];
					if (strpos($k,"dt_") === 0) {
						if ($v) {
							$v = $this->dt2Time($v);
						}
					} else if (strpos($k,"d_") === 0) {
						if ($v) {
							$v = $this->d2Time($v);
						}
					}
					$this->$k = $v;
				}
			}
		}
	}

	/**
	 * Store all object attributes in
	 * an associative array and return this.
	 *
	 * @return array
	 */
	function toArray() {
		foreach (get_object_vars($this) as $k => $v) {
			if ($k{0} != "_" && $k != "w") { // ignore volatile vars
				$arr[$k]=$v;
			}
		}
		return $arr;
	}

	/**
	 * Return a human readable formatted date
	 *
	 * @param <type> $var
	 * @param <type> $format
	 * @return <type> a formatted date
	 */
	function getDate($var,$format='d/m/Y') {
		if (array_key_exists($var, get_object_vars($this)) && $this->$var) {
			return $this->time2D($this->$var,$format);
		}
	}

	/**
	 *
	 * @param <type> $var
	 * @param <type> $format
	 * @return <type> formatted date and time
	 */
	function getDateTime($var,$format='d/m/Y H:i') {
		if (array_key_exists($var, get_object_vars($this)) && $this->$var) {
			return $this->time2Dt($this->$var,$format);
		}
	}

	/**
	 *
	 * @param <type> $var
	 * @param <type> $format
	 * @return <type> formatted date and time
	 */
	function getTime($var,$format=null) {
		if (array_key_exists($var, get_object_vars($this)) && $this->$var) {
			return $this->time2T($this->$var,$format);
		}
	}


	function setTime($var,$date) {
		if (array_key_exists($var, get_object_vars($this))) {
			$this->$var = $this->t2Time($date);
		}
	}
	/**
	 * Transform a human readable date into a timestamp to be
	 * stored in this object.
	 *
	 * @param <type> $var
	 * @param <type> $date
	 */
	function setDate($var,$date) {
		if (array_key_exists($var, get_object_vars($this))) {
			$this->$var = $this->d2Time($date);
		}
	}

	/**
	 * Transform a human readable date into a timestamp to be
	 * stored in this object.
	 *
	 * @param <type> $var
	 * @param <type> $date
	 */
	function setDateTime($var, $date) {
		if (array_key_exists($var, get_object_vars($this))) {
			$this->$var =  $this->dt2Time($date);
		}
	}

	/**
	 * Utility function to decide
	 * whether to insert or update
	 * an object in the database.
	 */
	function insertOrUpdate() {
		if ($this->id != null) {
			$this->update();
		} else {
			$this->insert();
		}
	}
	/**
	 * create and execute a sql insert statement for this object.
	 *
	 * @param <type> $table
	 */
	function insert() {
		$t = $this->getDbTableName();

		// set some default attributes
		if (!$this->_modifiable) {
			// for backwards compatibility
			if (property_exists($this, "dt_created")) {
				$this->dt_created = time();
			}
			if (property_exists($this, "creator_id") && $this->creator_id === null && $this->w->Auth->user()) {
				$this->creator_id = $this->w->Auth->user()->id;
			}
			if (property_exists($this, "dt_modified")) {
				$this->dt_modified = time();
			}
			if (property_exists($this, "modifier_id") && $this->w->Auth->user()) {
				$this->modifier_id = $this->w->Auth->user()->id;
			}
		}
		$data = array();
		foreach (get_object_vars($this) as $k => $v) {
			if ($k{0} != "_" && $k != "w" && $v !== null) {
				$dbk = $this->getDbColumnName($k);
				if (strpos($k,"dt_") === 0) {
					if ($v) {
						$v = $this->time2Dt($v);
						$data[$dbk]=$v;
					}
				} else if (strpos($k,"d_") === 0) {
					if ($v) {
						$v = $this->time2D($v);
						$data[$dbk]=$v;
					}
				} else if (strpos($k,"t_") === 0) {
					if ($v) {
						$v = $this->time2T($v);
						$data[$dbk]=$v;
					}
				} else if (strpos($k,"s_") === 0) {
					if ($v) {
						$v = encrypt($v,$this->__password);
						$data[$dbk]=$v;
					}
				} else {
					$data[$dbk]=$v;
				}
			}
		}
		
		$this->_db->insert($t,$data);
		if ($t != "audit") {
			$this->w->logAudit("".$this->_db->sql);
		}
		
		// echo $this->_db->print_sql();
		
		$this->_db->execute();
		$this->id = $this->_db->last_insert_id();

		// call standard aspect methods
		if ($this->_modifiable) {
			$this->_modifiable->insert();
		}
		if ($this->_versionable) {
			$this->_versionable->insert();
		}
		if ($this->_searchable) {
			$this->_searchable->insert();
		}
		
		// store this id in the context for listeners
		$inserts = $this->w->ctx('db_inserts');
		if (!$inserts) {
			$inserts = array();
		}
		$inserts[get_class($this)][]=$this->id;
		$this->w->ctx('db_inserts',$inserts);

		if ($this->__use_auditing) {
			// TODO remove dependency to user code!
			$this->w->Admin->addDbAuditLogEntry("insert",get_class($this),$this->id);
		}
	}

	/**
	 * Update an object
	 *
	 * @param $force if true set null values in db, if false, null values in object will be ignored
	 */
	function update($force=false) {
		$t = $this->getDbTableName();

		// check delete attribute
		if (property_exists($this,"is_deleted") && $this->is_deleted === null) {
			$this->is_deleted = 0;
		}

		// set default attributes the old way
		if (!$this->_modifiable) {
			if (property_exists($this, "dt_modified")) {
				$this->dt_modified = time();
			}
			if (property_exists($this, "modifier_id") && $this->w->Auth->user()) {
				$this->modifier_id = $this->w->Auth->user()->id;
			}
		}
		$data = array();
		foreach (get_object_vars($this) as $k => $v) {
			if ($k{0} != "_" && $k != "w") { // ignore volatile vars
				$dbk = $this->getDbColumnName($k);

				if (strpos($k,"dt_") === 0 ) {
					if ($v) {
						$v = $this->time2Dt($v);
						$data[$dbk]=$v;
					}
				} else if (strpos($k,"d_") === 0) {
					if ($v) {
						$v = $this->time2D($v);
						$data[$dbk]=$v;
					}
				} else if (strpos($k,"t_") === 0) {
					if ($v) {
						$v = $this->time2T($v);
						$data[$dbk]=$v;
					}
				} else if (strpos($k,"s_") === 0) {
					if ($v) {
						$v = encrypt($v,$this->__password);
						$data[$dbk]=$v;
					}
				} else {
					if ($v !== null) {
						$data[$dbk]=$v;
					}
				}
				// if $force is TRUE and $v is NULL, then set fields in DB to NULL
				// otherwise ignore NULL values
				if ($v === null && $force == true) {
					$data[$dbk]=null;
				}
			}
		}
		$this->_db->update($t,$data)->where($this->_cn('id'),$this->id);
		if ($t != "audit") {
			// TODO remove dependency to user code!
			$this->w->logAudit("".$this->_db->sql);
		}
		$this->_db->execute();

		// call standard aspect methods
		if ($this->_modifiable) {
			$this->_modifiable->update();
		}
		if ($this->_versionable) {
			$this->_versionable->update();
		}
		if ($this->_searchable) {
			$this->_searchable->update();
		}
		
		// store this id in the context for listeners
		$updates = $this->w->ctx('db_updates');
		if (!$updates) {
			$updates = array();
		}
		$updates[get_class($this)][]=$this->id;
		$this->w->ctx('db_updates',$updates);

		if ($this->__use_auditing) {
			// TODO remove dependency to modules code!
			$this->w->Admin->addDbAuditLogEntry("update",get_class($this),$this->id);
		}
	}

	/**
	 * create and execute a sql delete statement to delete this object from
	 * the database.
	 *
	 * @param $force
	 */
	function delete($force=false) {
		$t = $this->getDbTableName();

		// if an is_deleted property exists, then only set it to 1
		// and update instead of delete!
		if (property_exists(get_class($this), "is_deleted") && !$force) {
			$this->is_deleted = 1;
			$this->update();
		} else {
			$this->_db->delete($t)->where($this->_cn('id'),$this->id)->execute();
		}
		// store this id in the context for listeners
		$deletes = $this->w->ctx('db_deletes');
		if (!$deletes) {
			$deletes = array();
		}
		$deletes[get_class($this)][]=$this->id;
		$this->w->ctx('db_deletes',$deletes);

		// delete from search index
		if ($this->_searchable) {
			$this->_searchable->delete();
		}
		
		// TODO remove dependency to user code!
		$this->w->Admin->addDbAuditLogEntry("delete",get_class($this),$this->id);
	}

	/**
	 * Returns the table name where this object is
	 * stored
	 * 
	 * Uses either:
	 * 
	 * 1) the value of the property $_db_table (if it exists)
	 * 2) the name of the class (lowercase)
	 * 
	 * You can also override this function completely.
	 *
	 * @return String
	 */
	function getDbTableName() {
		if (property_exists($this, "_db_table") && $this->_db_table) {
			return $this->_db_table;
		}
		return strtolower(get_class($this));
	}

	/**
	 * Returns the column name for a named attribute
	 *
	 * @param <type> $attr
	 * @return <type>
	 */
	function getDbColumnName($attr) {
		return $attr;
	}

	function _tn() {
		return $this->getTableName();
	}

	function _cn($attr) {
		return $this->getDbColumnName($attr);
	}

	/**
	 * get Creator user object if creator_id
	 * property exists
	 * 
	 * @return User
	 */
	function & getCreator() {
		if ($this->_modifiable) {
			return $this->_modifiable->getCreator();
		} else if(property_exists(get_class($this), "creator_id")) {
			return $this->w->Auth->getUser($this->creator_id);
		} else {
			return null;
		}
	}
	/**
	 * get Modifier user object if creator_id
	 * property exists
	 * 
	 * @return User
	 */
	function & getModifier() {
		if ($this->_modifiable) {
			return $this->_modifiable->getModifier();
		} else if(property_exists(get_class($this), "modifier_id")) {
			return $this->w->Auth->getUser($this->modifier_id);
		} else {
			return null;
		}
	}
	
	/**
	 * Override this function if you want to add custom content
	 * to the search index for this object. 
	 * 
	 * DO NOT CALL $this->getIndexContent() within this function
	 * or you will create an endless loop which will destroy the universe!
	 * 
	 * @return String
	 */
	function addToIndex() {}
	
	/**
	 * If this object is searchable then this function returns
	 * the string that is generated by the AspectSearchable::getIndexContent()
	 * function.
	 * 
	 * You can use this function for adding joined objects together in one index, e.g.
	 * CrmContact and its associated Contact.
	 * 
	 * @return String
	 */
	function getIndexContent() {
		if (property_exists($this,"_searchable") && $this->_searchable) {
			return $this->_searchable->getIndexContent();
		}
	}
	
	
	/**
	 * Return an array of options for a field of this object.
	 * This can then be used to create selects dropdown lists or radiobuttons.
	 * The Html::form() and Html::multiColForm() functions will use this function
	 * to create a select option list if no other options are given in the parameters
	 * for this field.
	 * 
	 * There are 2 ways this function can be used ..
	 * 
	 * 1. You can just override it in your subclass and do what you want
	 * 2. You can use the automagic properties in your subclass explained below
	 * 
	 * The return of this function should be an array that is fit for passing to Html::select(), eg.
	 * 
	 * a) array("Option1", "Option2", ..)
	 * b) array(array("Title","Value"), array("Title","Value), ..)
	 * c) array($dbobject1, $dbobject2, ..)
	 * 
	 * Automagic UI Field Hints
	 * 
	 * static $_<fieldname>_ui_select_string = array("option1","option2",...);
	 * --> create a select dropdown using those strings explicitly
	 *
	 * static $_<fieldname>_ui_select_lookup_code = "states";
	 * --> create a select dropdown and filling it with Lookup items from the database
	 *     for the given code
	 *
	 * static $_<fieldname>_ui_select_objects_class = "Contact";
	 * static $_<fieldname>_ui_select_objects_filter = array("is_deleted"=>0);
	 * --> create a select filling it with the objects for the _class filtered by the _filter criteria
	 * 
	 * @param String $field
	 * @return array
	 */
	function getSelectOptions($field) {

		// check whether this field has hints
		$prop_string = "_".$field."_ui_select_string";
		$prop_lookup = "_".$field."_ui_select_lookup_code";
		$prop_class = "_".$field."_ui_select_objects_class";
		$prop_filter = "_".$field."_ui_select_objects_filter";
		
		if (property_exists($this, $prop_string) && is_array($this->$prop_string)) {
			return $this->$prop_string;
		} 
		else if (property_exists($this, $prop_lookup) && $this->$prop_lookup) {
			return $this->Admin->getLookupItemsbyType($this->$prop_lookup);
		} 
		else if (property_exists($this, $prop_class) && $this->$prop_class) {
			if (property_exists($this, $prop_filter) && $this->$prop_filter) {
				return $this->getObjects($this->$prop_class, $this->$prop_filter, true);
			} else {
				return $this->getObjects($this->$prop_class, null, true);
			}
		}
	}
}
