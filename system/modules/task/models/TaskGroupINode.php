<?php
use Sabre\DAV;

/**
 * INode wrapper for a Task Group
 * override of DBObjectINode to add  tasks as children
 */
class TaskGroupINode extends DBObjectINode {
	
	/**
	 * Returns an array of File and/or Directory objects.
	 */
	function getChildren() {
		
		$result=parent::getChildren();
		//return $result;	
		$children=$this->getDBObject()->getTasks();
		if (is_array($children)) {
			foreach ($children as  $object) {
				if ($object->canList($this->w->Auth->user())) {
					$iNode=new TaskINode($this->w,$object);
					$result[]=$iNode;
				}
			}
		}
		return $result;
	} 
	
	/**
	 * Returns a File or Directory object for the child-node of the given name.
	 */
	 
	function getChild($name) {
			
		if (!empty($name)) {
			$children=$this->getDBObject()->getTasks();
			if (is_array($children)) {
				foreach ($children as  $object) {
					if ($object->getSelectOptionTitle()==$name) {
						if ($object->canView($this->w->Auth->user())) {
							$result=new TaskINode($this->w,$object);
						} else {
							throw new Exception('No access to this task');
						}
					}
				}
			}
		
			if (empty($result)) {
				$result=parent::getChild($name);
			}
		
			if (empty($result)) throw new Exception('No matching child '.$name);
			return $result;
		}
	}
}
