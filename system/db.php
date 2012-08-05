<?php
require_once "crystal-0.4/Crystal.php";

/**
 * General Service class for subclassing in modules.
 * 
 * @author carsten
 *
 */
class DbService {
    var $_db;
    var $w;

    /**
     * array for automatic caching of objects for the duration of this
     * invocation.
     * 
     * @var <type>
     */
    private static $_cache = array(); // used for single objects
    private static $_cache2 = array();  // used for lists of objects
    
    /**
     * This variable keeps track of active transactions
     * 
     * @var boolean
     */
    private static $_active_trx = false;

    /**
     * set this variable to false if you don't want
     * the getObject, getObjects, etc. to use the
     * cache.
     * 
     * @var <type>
     */
    private $__use_cache = true;

    public function __get($name) {
    	return $this->w->$name;
    }
    
    function __construct(Web $w) {
        $this->_db = $w->db;
        $this->w = $w;
    }
    
    /**
     * Formats a timestamp
     * per default MySQL datetime format is used
     * 
     * @param $time
     * @param $format
     */
    function time2Dt($time=null,$format='Y-m-d H:i:s') {
        return formatDate($time ? $time : time(),$format,false);
    }

    /**
     * Formats a timestamp
     * per default MySQL date format is used
     * 
     * @param $time
     * @param $format
     */
    function time2D($time=null,$format='Y-m-d') {
        return formatDate($time ? $time : time(),$format,false);
    }

    function time2T($time=null,$format='H:i:s') {
        return date($format,$time ? $time : time());
    }

    function dt2Time($dt) {
        return strtotime(str_replace("/","-",$dt));
    }

    function d2Time($d) {
        return strtotime(str_replace("/","-",$d));
    }

    function t2Time($d) {
        return strtotime(str_replace("/","-",$d));
    }

    /**
     * Clear object cache completely!
     * 
     */
    function clearCache() {
    	self::$_cache = array();
    	self::$_cache2 = array();
    }
    /**
     * Fetch one object either by id
     * or by passing an array of key,value
     * to be used in a where condition
     *
     * @param <type> $table
     * @param <type> $idOrWhere
     * @return <type>
     */
    function & getObject($class,$idOrWhere) {
        if (!$idOrWhere || !$class) return null;

        $key = $idOrWhere;
        if (is_array($idOrWhere)) {
        	$key = "";
        	foreach ($idOrWhere as $k=>$v) {
        		$key.=$k."::".$v."::";
        	}
        }
        $usecache = $this->__use_cache && is_scalar($key);
        // check if we should use the cache
        // this will eliminate 80% of SQL calls per page view!
        if ($usecache) {
            $obj = self::$_cache[$class][$key];
            if ($obj) {
        		//$this->w->logDebug("cached object:".$class.", ".$idOrWhere);
            	return $obj;
            }
        }

        // not using cache or object not found in cache
        //$this->w->logDebug("load object:".$class.", ".$idOrWhere);
        
        $o = new $class($this->w);
        $table = $o->getDbTableName();
        if (is_scalar($idOrWhere)) {
            $result = $this->_db->get($table)->where('id',$idOrWhere)->fetch_row();
        } elseif (is_array($idOrWhere)) {
            $result = $this->_db->get($table)->where($idOrWhere)->fetch_row();
        }
        if ($result) {
            $obj = $this->getObjectFromRow($class, $result);
            if ($usecache) {
                self::$_cache[$class][$key]=$obj;
                if ($obj->id != $key && !self::$_cache[$class][$obj->id]) {
                	self::$_cache[$class][$obj->id]=$obj;
                }
            }
            return $obj;
        } else {
            return null;
        }
    }

    /**
     *
     * @param <type> $table
     * @param <type> $id
     * @return <type>
     */
    function & getObjects($class,$where=null,$useCache = false) {
        if (!$class) return null;// !$where || will return null as developers is assume ommiting the where clause will give them everything 

        // create a cache key just in case
        if (is_array($where)) {
			foreach ($where as $k=>$v) {
	       		$key.=$k."::".$v."::";
	       	}    
        } else {
        	$key = $where;
        }
        
        if ($useCache && self::$_cache2[$class][$key]) {
        	return self::$_cache2[$class][$key];
        }
        
        $o = new $class($this->w);
        $table = $o->getDbTableName();
        $this->_db->get($table);
        if ($where && is_array($where)) {
			foreach ($where as $par => $val) {
	        	$dbwhere[$o->getDbColumnName($par)]=$val;
	        }
        	$this->_db->where($dbwhere);
        } else if ($where && is_scalar($where)) {
        	$this->_db->where($where,false);			//if no $where, only $this->_db->get($get)->fetch_all() will be called
        }       											
        $result = $this->_db->fetch_all();
        if ($result) {
            $objects = $this->fillObjects($class, $result);
            if ($objects && $this->__use_cache) {
            	
            	// only store the full list if requested
            	if ($useCache) {
		            
		        	if (!self::$_cache2[$class][$key]) {
		        		self::$_cache2[$class][$key] = $objects;
		        	}
            	}
            	
            	// always store individual objects in cache
            	foreach ($objects as $ob) {
            		if (!self::$_cache[$class][$ob->id]) {
            			self::$_cache[$class][$ob->id] = $ob;
            		}
            	}
            }
            return $objects;
        } else {
            return null;
        }
    }

    /**
     *
     * @param <type> $table
     * @param <type> $id
     * @return <type>
     */
    function & getObjectFromRow($class,$row) {
        if (!$row || !$class) return null;
        $o = new $class($this->w);
        $o->fill($row);
        return $o;
    }

    function & fillObjects($class, $rows) {
        $list = array();
        if ($class && $rows && class_exists($class)) {
            foreach($rows as $row) {
                $o = new $class($this->w);
                $o->fill($row);
                $list[]=$o;
            }
        }
        return $list;
    }

    /**
     * Start a transaction
     * 
     */
    function startTransaction() {
    	$this->_db->sql("START TRANSACTION")->execute();
    	self::$_active_trx = true;
    }
    
    /**
     * Commit a transaction
     * 
     */
    function commitTransaction() {
    	$this->_db->sql("COMMIT")->execute();
    	self::$_active_trx = false;
    }
    
    /**
     * Rollback a transaction!
     * This includes a clear_sql()!
     */
    function rollbackTransaction() {
    	$this->_db->clear_sql();
    	$this->_db->sql("ROLLBACK")->execute();
    	self::$_active_trx = false;
    }
    
    /**
     * Returns true if a transaction is currently active!
     * 
     */
    function isActiveTransaction() {
    	return self::$_active_trx;
    }
    
    
    function lookupArray($type) {
    	$rows = $this->_db->select("code,title")->from("lookup")->where("type",$type)->fetch_all();
    	foreach ($rows as $row) {
    		$select[$row['code']]=$row['title'];
    	}
    	return $select;
    }
    
}

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
 * 0. _* properties are considered transient not automatically
 *    saved to DB
 *     
 * 1. dt_* any property starting with dt_ will be transformed 
 *    into seconds when loaded from DB and turned back into
 *    MySQL datetime format when saved to DB.
 * 
 * 2. d_* as above but data only
 * 
 * 3. t_* as aboce but time only
 *
 * 4. is_deleted when exists will be set to 1 instead of
 *    deleting the table data on object::delete(), to really
 *    delete the data in the DB ::delete(true) must be called!
 *    
 * 5. title when exists will be automatically used for 
 *    object::getSelectOptionTitle() method
 *    
 * 6. name when exists and title doesn't exist then will be used
 *    for object::getSelectOptionTitle() method
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
 * 3. Auditing of inserts and updates happens automatically to an audit table.
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
    //var $_modifiable;
    
    //Define this property if you want to use the
    //VersionableAspect
    //var $_versionable;
    
    /**
     * Constructor
     * 
     * @param $w
     */
    function __construct(Web &$w) {
    	parent::__construct($w);
    	
    	// add standard aspects
    	if (property_exists($this,"_modifiable")) {
    		$this->_modifiable = new AspectModifiable($this);
    	}
    	if (property_exists($this,"_versionable")) {
    		$this->_versionable = new AspectVersionable($this);
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
     * @deprecated use getSelectOptionTitle() instead
     */
    function selectTitle() {
    	return $this->_selectOptionTitle();
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
    	return $this->selectTitle(); // only until all references are resolved
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
    function canList(User &$user) {
        return true;
    }

    /**
     * used by the search display function to check whether the user has
     * permission to view further details about this item.
     *
     * @param User $user
     */
    function canView(User &$user) {
        return true;
    }

    /**
     * can be used by other function to check whether the user has
     * permissions to edit this item.
     *
     * @param User $user
     */
    function canEdit(User &$user) {
        return true;
    }

    /**
     * can be used by other functions to check whether this user has
     * permissions to delete this item.
     *
     * @param User $user
     */
    function canDelete(User &$user) {
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
        $this->_db->execute();
        $this->id = $this->_db->last_insert_id();

        // call standard aspect methods
        if ($this->_modifiable) {
        	$this->_modifiable->insert();
        }
        if ($this->_versionable) {
        	$this->_versionable->insert();
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
        
        // TODO remove dependency to user code!
		$this->w->Admin->addDbAuditLogEntry("delete",get_class($this),$this->id);
     }

    /**
     * Returns the table name where this object is
     * stored
     *
     * @return <type>
     */
    function getDbTableName() {
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
}


//////////////////////////////////////////////////////////////////////////////
//            Generic Modification data for some objects
//////////////////////////////////////////////////////////////////////////////

/**
 * Store creation and modification data of any object
 */
class ObjectModification extends DbObject {
	var $table_name;
	var $object_id;
	
	var $dt_created;
	var $dt_modified;
	var $creator_id;
	var $modifier_id;
	
	// do not audit this table!
	var $__use_auditing = false;

	/**
	 * returns the creator of the 
	 * object which is attached to this
	 * aspect.
	 * 
	 * @return User
	 */
	function & getCreator() {
		if ($this->creator_id) {
			return $this->w->Auth->getUser($this->creator_id);
		}
	}

	/**
	 * returns the modifier user
	 * of the object which is attached
	 * to this aspect.
	 * 
	 * @return User
	 */
	function & getModifier() {
		if ($this->modifier_id) {
			return $this->w->Auth->getUser($this->modifier_id);
		}
	}
	
	function getCreatedDate() {
		return $this->dt_created;
	}

	function getModifiedDate() {
		return $this->dt_modified;
	}

	function getDbTableName() {
        return "object_modification";
    }
}

/**
 * Use this aspect to store creation
 * and modification data for any object
 * @author carsten
 *
 */
class AspectModifiable {
	private $object;
	private $_mo;
	
	function __construct(DbObject &$obj) {
		$this->object = $obj;
	}
	
	private function & getMo() {
		if ($this->object && $this->object->id && !$this->_mo) {
			$this->_mo = $this->object->getObject("ObjectModification", array("table_name"=>$this->object->getDbTableName(), "object_id"=>$this->object->id));
		}
		return $this->_mo;
	} 

    /////////////////////////////////////////////////
    // Methods to be used by DbObject
    /////////////////////////////////////////////////
	
	/**
	 * Store creation data
	 */
	function insert() {
		if (!$this->getMo()) {
			$mo = new ObjectModification($this->object->w);
			$mo->table_name = $this->object->getDbTableName();
			$mo->object_id = $this->object->id;
			$mo->dt_created = time();
			$mo->creator_id = $mo->w->Auth->user()->id;
			$mo->insert();
		}
	}
	
	/**
	 * Store modification data
	 */
	function update() {
		if ($this->getMo()) {
			$this->_mo->dt_modified = time();
			$this->_mo->modifier_id = $this->_mo->w->Auth->user()->id;
			$this->_mo->update();
		}
	}
	
    /////////////////////////////////////////////////
    // Methods to be used by client object
    /////////////////////////////////////////////////
    
	function & getCreator() {
		if ($this->getMo()) {
			return $this->_mo->getCreator();
		}
	}

	function & getModifier() {
		if ($this->getMo()) {
			return $this->_mo->getModifier();
		}
	}
	
	function getCreatedDate() {
		if ($this->getMo()) {
			return $this->_mo->getCreatedDate();
		}
	}

	function getModifiedDate() {
		if ($this->getMo()) {
			return $this->_mo->getModifiedDate();
		}
	}
	
	
}

//////////////////////////////////////////////////////////////////////////////
//            Automatic History for some objects
//////////////////////////////////////////////////////////////////////////////

/**
 * Stores data about updates to any object
 */
class ObjectHistory extends DbObject {
    var $class_name;
    var $object_id;

    // has ModifiableAspect!!
    var $__is_modifiable;
    
    // do not audit this table!
    var $__use_auditing = false;
    
    function getDbTableName() {
        return "object_history";
    }

    function getCurrentObject() {
        return $this->getObject($this->class_name, $this->object_id);
    }

    function getValues() {
        return $this->getObjects("ObjectHistoryEntry", array("history_id",$this->id));
    }
    
    function fillFromObject($obj) {
    	
    }
}

/**
 * stores single field values for a historic object
 */
class ObjectHistoryEntry extends DbObject {
    var $history_id;
    var $attr_name;
    var $attr_value;

    // do not audit this table!
    var $__use_auditing = false;
    
    function getDbTableName() {
        return "object_history_entry";
    }
}

/**
 * Implementing the Versionable Aspect which uses 
 * ObjectHistory entries for storing versions
 * of an object
 */
class AspectVersionable {
	private $object;
	private $create_version = true;
	
	function __construct(DbObject &$obj) {
		$this->object = $obj;
	}

    /////////////////////////////////////////////////
    // Methods to be used by DbObject
    /////////////////////////////////////////////////
	
    /**
     * this update function will create a new historic object
     * for these values before updating the current record
     */
    function update() {
        // save all values in a new historic object before updating
        if ($this->create_version) {
	        $ho = new ObjectHistory($this->object->w);
	        $ho->fillFromObject($this->object);
	    	$ho->insert();
        }
    }

    /**
     * this update function will create a new historic object
     * for these values before updating the current record
     */
    function insert() {
        $ho = new ObjectHistory($this->object->w);
        $ho->fillFromObject($this->object);
    	$ho->insert();
    }

    /////////////////////////////////////////////////
    // Methods to be used from client objects
    /////////////////////////////////////////////////

    
    /**
     * get all history data for this object
     */
    function getAllVersions() {
        return null;
    }

    /**
     * get a specific version by id
     * @param $id
     */
    function getVersion($id) {
    	return null;
    }
    
    /**
     * get the very latest version
     */
    function getLastVersion() {
    	return null;
    }
}

