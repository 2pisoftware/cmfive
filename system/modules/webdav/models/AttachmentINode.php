<?php
use Sabre\DAV;
/**
 * Wrapper around an attachment to implement iFile for sabre\dav
 */
class AttachmentINode implements Sabre\DAV\IFile {
	
	use DbObjectTrait;
	
	/**
	 * updates the data in the file.
	 */
	function put($data) {
		if ($this->getDBObject()->canEdit($this->w->Auth->user())) {
			return $this->getDBObject()->getFile()->setContent($data); 
		} else  {
			throw new Exception('No write access to this object');
		}
	}
	
	/**
	 * returns the contents of the file.
	 */
	 function get() {
		if ($this->getDBObject()->canView($this->w->Auth->user())) {
			return $this->getDBObject()->getFile()->getContent(); 
		} else {
			throw new Exception('No read access to this object');
		}
	}
	
	/**
	 * returns the file/directory name.
	 */
	function getName() {
		return $this->getDBObject()->filename;
	} 
	

	
	/**
	 * returns a unique identifier of the current state of the file. If the file changes, so should the etag. Etags are surrounded by quotes.
	 */
	function getETag() {
		$o=$this->getDBObject();
		$date=$date=$o->dt_modified;
		return hash('md5',get_class($o).$o->id.$date);
	} 
	
	/**
	 * Returns the mime-type of the file.
	 */
	function getContentType() {
		return $this->getDBObject()->getMimetype(); 
	} 
	
	/**
	 * returns the size in bytes.
	 */
	function getSize() {
		return $this->getDBObject()->getFile()->getSize(); 
	} 
	
	/**
	 * deletes the file/directory.
	 */
	function delete() {
		return $this->getDBObject()->delete();
	} 
}
