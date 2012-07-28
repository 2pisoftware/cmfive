<?php
class WikiPage extends DbObject {
	var $name;
	var $wiki_id;
	var $dt_created;
	var $dt_modified;
	var $creator_id;
	var $modifier_id;
	var $is_deleted;
	var $body;

	function & getWiki() {
		return $this->Wiki->getWikiById($this->wiki_id);			
	}
	
	function canList($user) {
		try {
			$wiki = $this->getWiki();
			return $wiki->canRead($user);
		} catch (WikiException $ex) {
			return false;
		}
	}
	
	function & getHistory() {
		return $this->getObjects("WikiPageHistory",array("wiki_page_id"=>$this->id));
	}
	
	function canView($user) {
		return $this->canList($user);
	}
	
	function printSearchListing() {
		$txt = "Last Modified: ";
		$txt .= formatDateTime($this->dt_modified);
		$txt .= " by ".$this->Auth->getUser($this->modifier_id)->getFullName();
		return $txt;				
	}
	
	function printSearchTitle() {
		return $this->getWiki()->title.", ".$this->name;
	}
	
	function printSearchUrl() {
		return "wiki/view/".$this->getWiki()->name."/".$this->name;
	}
	
	function update() {
		$hist = new WikiPageHistory($this->w);
		$hist->fill($this->toArray());
		$hist->id = null;
		$hist->wiki_page_id = $this->id;
		$hist->insert();
		parent::update();
	}

	function insert() {
		parent::insert();
		$hist = new WikiPageHistory($this->w);
		$hist->fill($this->toArray());
		$hist->id = null;
		$hist->wiki_page_id = $this->id;
		$hist->insert();
	}

	function getDbTableName() {
		return "wiki_page";
	}

	function getHtml() {
		return $this->body;
	}

}
