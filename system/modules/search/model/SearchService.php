<?php
class SearchService extends DbService {
	function getSearchIndexes() {
		$idx = array();
		foreach ($this->w->moduleConf("search", "index") as $title => $conf) {
			if ($this->w->Auth->allowed($conf['permission'])) {
				$idx[]=array($title, $conf['index']);
			}
		}
		return $idx;
	}

	function & getObjectForIndex($index, $id) {
		$table = str_replace("idx_", "", $index);
		return $this->getObject($table, $id);
	}
}