<?php
use Sabre\DAV;

/**
 * Collection representing a record that lists attachments and 
 * potentially other related records as children
 */
class DBObjectINode extends Sabre\DAV\Collection {
	
	use DbObjectTrait;
	
	
}

/**
 * Traits to implement DBObjectInode
 */
trait DbObjectTrait {	
	
	public $collateBy;
	private $object;
	protected $w;
	
	/**
	 * Constructor
	 */
	 function __construct($w,$object) {
		if (!is_a($object,'DbObject')) {
			throw new Exception('You must provide a DBObject to the INode constructor');
		}
		$thisINodeClass=substr(get_class($this),0,strlen(get_class($this))-5);
		if (get_class($object)!=$thisINodeClass) {
			throw new Exception('You must provide a matching DBObject to the INode constructor. ie '.$thisINodeClass.'INode must be passed a '.$thisINodeClass.' rather than a '.get_class($object));
		}
		$this->object=$object;
		$this->w=$w;
	}
	
	/**
	 * Get the Cmfive object behind this inode
	 */
	function getDBObject() {
		return $this->object;
	}
	
	/**
	 * deletes the file/directory.
	 */
	function delete($force=false) {
		//return $this->getDBObject()->delete($force);
	} 
	
	/**
	 * returns the file/directory name.
	 */
	function getName() {
		$gen= $this->getDBObject()->getSelectOptionTitle();
		if (strlen(trim($gen))>0) {
			return $gen;
		} else {
			return '!!Failed to generate title!!';
		}
	} 
	
	/**
	 * renames the directory.
	 * override this if you want webdav to set title or header in records
	 */
	function setName($newName) {} 
	
	/**
	 * returns the last modification time as a unix timestamp.
	 */
	function getLastModified() {
		$object=$this->getDBObject();
		if (property_exists($object,'dt_modified')) {
			return $object->dt_modified;
		} else if (property_exists($object,'dt_created')) {
			return $object->dt_created;
		} else {
			return 0;
		}
	}
	
	/**
	 *  Returns true if a child node exists.
	 */
	function childExists($name) {
		if (!empty($this->getChild($name))) {
			return true;
		} else {
			return false;
		}
	}
	

	
	/**
	 * Returns an array of File and/or Directory objects.
	 */
	function getChildren() {
		$result=[];
		$children=$this->w->File->getAttachments($this->getDBObject(),$this->getDBObject()->id);
		if (is_array($children)) {
			foreach ($children as  $object) {
				$iNode=new AttachmentINode($this->w,$object);
				$result[]=$iNode;
			}
		}
		return $result;
	} 

	/**
	 * Returns a File or Directory object for the child-node of the given name.
	 */
	function getChild($name) {
		$children=$this->getChildren();
		if (is_array($children)) {
			foreach ($children as  $node) {
				$object=$node->getDBObject();
				if ($object->getSelectOptionTitle()==$name) {
					if ($object->canView($this->w->Auth->user())) {
						$result=$node;
					} else {
						throw new Exception ('No access to this record');
					}
				}
			}
		}
		if (empty($result)) {
			throw new Exception('No matching child '.$name);
		}
		return $result;
	}
		
	/**
	 * Creates a new file with the given name.
	 */
	function createFile($name,$data = NULL) {
		$this->w->File->saveFileContent($this->getDBObject(), $data, $name);
	} 
	
	/**
	 *  Creates a subdirectory with the given name.
	 */
	function createDirectory($name) {}
	

}



