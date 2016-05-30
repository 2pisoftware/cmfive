<?php
use Sabre\DAV;

/**
 * Collection representing a record type that lists records as children
 */
class ClassINode extends Sabre\DAV\Collection {
	
	private $className;
	private $w;
	
	function __construct($w,$className) {
		$this->className = $className;
		$this->w=$w;
	}
	
	/**
	 * returns the file/directory name.
	 */
	function getName() {
		return $this->className;
	} 
	
	/**
	 *  Returns true if a child node exists.
	 */
	function childExists($name) {
		$object=$this->w->iNode->getObject($this->className,$name);
		if (!empty($object)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns a File or Directory object for the child-node of the given name.
	 */
	function getChild($name) {
		$result=null;
		$children=$this->getChildren();
		if (is_array($children)) {
			foreach ($children as  $iNode) {
				$object=$iNode->getDBObject();
				if ($object->getSelectOptionTitle()==$name) {
					if ($object->canView($this->w->Auth->user())) {
						$result=$iNode;
					} else {
						throw new Exception('No access to this '.get_class($object));
					}
				}
			}
		}
		return $result;
	} 
	
	/**
	 * Returns an array of File and/or Directory objects.
	 */
	function getChildren() {
		$result=[];
		$children=$this->w->iNode->getObjects($this->className,[]); //['deleted'=>'0']
		if (is_array($children)) {
			foreach ($children as  $object) {
				if ($object->canList($this->w->Auth->user())) {
					$iNodeName=$this->className."INode";
					$iNode=new $iNodeName($this->w,$object);
					if (strlen(trim($iNode->collateBy))>0) {
						$collateBy=$iNode->collateBy;
						$result[$object->$collateBy]=$iNode;
					} else {
						$result[]=$iNode;
					}
				}
			}
		}
		//print_r([count($result)]);
		//die();
		//throw new Exception('eee');
		return $result;
	} 
	
	/**
	 * returns the last modification time as a unix timestamp.
	 */
	function getLastModified() {
		return 0;
	}
	
	/********************************************
	 * NOT IMPLEMENTED for RO filesystem
	 *******************************************/
	//function delete() {} 
	//function setName($newName) {} 
	//function createFile($name,$data) {} 
	//function createDirectory($name) {}
	
}
