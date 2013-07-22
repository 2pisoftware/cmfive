<?php
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

