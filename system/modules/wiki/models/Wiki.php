<?php
class Wiki extends DbObject{
	var $title;
	var $name;
	var $dt_created;
	var $creator_id;
	var $dt_modified;
	var $modifier_id;

	var $is_deleted;
	var $owner_id;
	var $is_public;
	var $last_modified_page_id;

	function getHistory() {
		$sql="
		SELECT DISTINCT name, creator_id, DAY( dt_created ) as day , 
		MONTH( dt_created ) as month, YEAR( dt_created ) as year
		FROM wiki_page_history
		WHERE YEAR( dt_created ) > 0 and wiki_id = ".$this->id." order by year,month,day,name desc";
		
		return $this->_db->sql($sql)->fetch_all();
	}
	function & getHomePage() {
		return $this->getPage($this->id,"HomePage");
	}

	function & getPage($name) {
		return $this->getObject("WikiPage",
		array("is_deleted"=>0,
				  "wiki_id"=>$this->id,
				  "name"=>$name));
	}
	function & getPageById($id) {
		return $this->getObject("WikiPage",
		array("is_deleted"=>0,
				  "id"=>$id));
	}

	function getName() {
		return ucfirst(str_replace(" ","",$this->title));
	}

	function insert() {
		if (!$this->title) {
			throw new WikiException("This wiki needs a title.");
		}
		$this->name = $this->getName();
		$this->owner_id = $this->w->Auth->user()->id;

		// check if wiki of the same name exists!
		$ow = $this->Wiki->getWikiByName($this->getName());
		if ($ow) {
			throw new WikiExistsException("Wiki of name ".$this->getName()." already exists.");
		}
		parent::insert();
		$this->addPage("HomePage", "= This is the HomePage =");
		$this->addUser($this->w->Auth->user(),"editor");
	}

	function & updatePage($name,$body) {
		$p = $this->getPage($name);
		if ($p) {
			$p->body = $body;
			$p->update();
			$this->last_modified_page_id = $p->id;
			$this->update();
		}
		return $p;
	}

	function & addPage($name,$body) {
		$p = new WikiPage($this->w);
		$p->wiki_id = $this->id;
		$p->name = $name;
		$p->body = $body;
		$p->insert();
		$this->last_modified_page_id = $p->id;
		$this->update();
		return $p;
	}

	function & getUsers() {
		return $this->getObjects("WikiUser",array("wiki_id"=>$this->id));
	}

	function canRead($user) {
		$wu = $this->getObject("WikiUser",array("user_id"=>$user->id,"wiki_id"=>$this->id));
		return $wu != null && ($this->isOwner($user) || $wu->role == "reader" || $wu->role == "editor");
	}
	
	function canEdit($user) {
		$wu = $this->getObject("WikiUser",array("user_id"=>$user->id,"wiki_id"=>$this->id));
		return $wu != null && ($this->isOwner($user)  || $wu->role == "editor");
	}
	
	function isUser($user) {
		$wu = $this->getObject("WikiUser",array("user_id"=>$user->id,"wiki_id"=>$this->id));
		return $wu != null;	
	}
	
	function addUser($user,$role="reader") {
		if (!$this->isUser($user)) {
			$wu = new WikiUser($this->w);
			$wu->wiki_id = $this->id;
			$wu->user_id = $user->id;
			$wu->role = $role;
			$wu->insert();
		}
	}

	/**
	 * get a WikiUser object by WikiUser::id
	 * 
	 * @param int $id
	 */
	function & getUserById($id) {
		return $this->getObject("WikiUser",$id);
	}
	
	/**
	 * remove a wiki user by the wiki_user::id
	 *
	 */
	function removeUser($id) {
		$wu = $this->getUserById($id);
		if ($wu) {
			$wu->delete();
		}
	}
	
	function isOwner($user) {
		return $this->owner_id == $user->id;
	}
}
