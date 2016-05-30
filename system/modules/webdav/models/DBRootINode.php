<?php
use Sabre\DAV;
/**
 * Container listing configured available objects
 */
class DBRootINode extends Sabre\DAV\Collection {
	
	private $children;
	private $w;
	
	function __construct($w) {
		$webdavConfig=Config::get('webdav');
		$this->children = $webdavConfig['availableObjects'];
		$this->w=$w;
	}
	
	/**
	 * returns the file/directory name.
	 */
	function getName() {
		return 'DB';
	} 
	
	/**
	 *  Returns true if a child node exists.
	 */
	function childExists($name) {
		if (array_key_exists($name,$this->children)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns a File or Directory object for the child-node of the given name.
	 */
	function getChild($name) {
		
		$webDavConfig=Config::get('webdav');
		
		// filesystem
		foreach ($webDavConfig['filesystems'] as $path) {
			$parts=explode('/',ROOT_PATH.$path);
			if ($name==$parts[count($parts)-1]) {
				return new DAV\FS\Directory(ROOT_PATH.$path);
			}
		}
		
		// db record
		$iNode=new ClassINode($this->w,$name);
		return $iNode;
	} 
	
	/**
	 * Returns an array of File and/or Directory objects.
	 */
	function getChildren() {
		$result=[];
		// configured root classes
		foreach ($this->children as $name => $details) {
			$iNode=new ClassINode($this->w,$name);
			$result[]=$iNode;
		}
		
		// filesystems
		$webDavConfig=Config::get('webdav');
		foreach ($webDavConfig['filesystems'] as $path) {
			$result[]=new DAV\FS\Directory(ROOT_PATH.$path);
		}
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
