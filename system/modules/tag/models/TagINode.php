<?php
use Sabre\DAV;

/**
 * INode wrapper for a user
 */
class TagINode extends DBObjectINode {
	
	public $collateBy='tag';  // ensure that the list of these objects is unique by tag name
	/**
	 * Returns an array of File and/or Directory objects.
	 */
	function getChildren() {
		$parentObject=$this->getDBObject();
		$children=$this->w->INode->getObjects('Tag',['tag'=>$parentObject->tag]);
		if (is_array($children)) {
			foreach ($children as  $tag) {
				$object=$this->w->INode->getObject($tag->obj_class,$tag->obj_id);
				if ($object->canList($this->w->Auth->user())) {
					$iNodeClass=$tag->obj_class."INode";
					$iNode=new $iNodeClass($this->w,$object);
					$result[]=$iNode;
				}
			}
		}
		
		return $result;
	} 
	

	
	

}
