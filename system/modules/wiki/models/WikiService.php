<?php
class WikiService extends DbService {

	function getWikiById($wiki_id) {
		$wiki = $this->getObject("Wiki",$wiki_id);
		if ($wiki && $wiki->canRead($this->w->Auth->user())) {
			return $wiki;
		} else {
			throw new WikiNoAccessException("You have no access to this wiki.");
		}
	}

	function getWikiByName($name) {
		$wiki = $this->getObject("Wiki",array("name"=>$name));
		if (!$wiki) return null;
		
		if ($wiki->is_public || $wiki->canRead($this->w->Auth->user())) {
			return $wiki;
		} else {
			throw new WikiNoAccessException("You have no access to this wiki.");
		}
	}

	function getWikis() {
		if ($this->w->Auth->user()->is_admin) {
			return $this->getObjects("Wiki",array("is_deleted"=>0));
		} else {
			$wus = $this->getObjects("WikiUser", array("user_id",$this->w->Auth->user()->id));
			if (!$wus) {
				return null;
			}
			foreach ($wus as $wu) {
				$wikis[] = $this->getObject("Wiki",$wu->wiki_id);
			}
			return $wikis;
		}
	}

	function createWiki($title, $is_public) {
		$wiki = new Wiki($this->w);
		$wiki->title = $title;
		$wiki->is_public = $is_public ? 1 : 0;
		$wiki->insert();
		return $wiki;
	}

}
