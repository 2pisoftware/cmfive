<?php
class WikiPage extends DbObject {
	
	public $_searchable;
	public $_exclude_index = array("is_deleted");
	
	public $name;
	public $wiki_id;
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	public $is_deleted;
	public $body;

	function getWiki() {
		return $this->Wiki->getWikiById($this->wiki_id);			
	}
	
	function canList(User $user) {
		try {
			$wiki = $this->getWiki();
			return $wiki->canRead($user);
		} catch (WikiException $ex) {
			return false;
		}
	}
	
	function getHistory() {
		return $this->getObjects("WikiPageHistory",array("wiki_page_id"=>$this->id));
	}
	
	function canView(User $user) {
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
	
	function update($force_null_values = false, $force_validation = false) {
		$hist = new WikiPageHistory($this->w);
		$hist->fill($this->toArray());
		$hist->id = null;
		$hist->wiki_page_id = $this->id;
		$hist->insert();
		parent::update();
	}

	function insert($force_validation = false) {
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
