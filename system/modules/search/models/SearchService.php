<?php
class SearchService extends DbService {

	/**
	 * Returns an array of search indeces that the currently logged in user
	 * has access to. This uses the global module configuration and auth system
	 * to check.
	 * 
	 * @return array
	 */
	public function getIndexes() {
		$indexes = array();
		
		foreach ($this->w->_moduleConfig as $module => $params) {
			if (array_key_exists("search", $params)) {
				$search = $params['search'];
				$indexes = array_merge($indexes,$search);
			}
		}
		asort($indexes);
		return $indexes;
	}
	
	/**
	 * Returns the  results for a given query.
	 * 
	 * If $index !== null -> limit search to this index.
	 * 
	 * If $page !== null -> display only results for this page.
	 * 
	 * @param string $query
	 * @param string $index
	 * @param integer $page
	 * @param integer $pageSize
	 * 
	 * @return array
	 */
	public function getResults($query, $index = null, $page = null, $pageSize = null) {
		
		// sanity check
		if (empty($query) || strlen($query) < 3) {
			return null;
		}
		
		// sanitise query string!
		// Remove all xml/html tags
		$str = strip_tags($query);
		
		// Remove case
		$str = strtolower($str);
		
		// Remove line breaks
		$str = str_replace("\n", " ", $str);
		
		// Remove all characters except A-Z, a-z, 0-9, dots, commas, hyphens, spaces and forward slashes (for dates)
		// Note that the hyphen must go last not to be confused with a range (A-Z)
		// and the dot, being special, is escaped with backslash
		$str = preg_replace("/[^A-Za-z0-9 \.,\-\/@'\*\+:]/", '', $str);
		
		// Replace sequences of spaces with one space
		$str = preg_replace('/  +/', ' ', $str);

		$index_mode = "BOOLEAN MODE";
		$index_all_limit = 10;
		
		$select = "SELECT class_name, object_id ";
        $from = " FROM object_index WHERE MATCH (content) AGAINST ('$str' IN $index_mode) ";
        $select .= $from;
        
		$select_count = "select count(*) as MAX_RESULT ".$from;
		$max_result = 0;
		// check if search is constrained to an index
		if ($index && in_array($index, array_values($this->getIndexes()))) {
			
			$select .= " AND class_name = '".$index."' ";
		
			// check pagination
			// only if searching for a particular index does pagination matter
			if ($page && is_numeric($page) && $pageSize && is_numeric($pageSize)) {
				// todo adam
				$select .= " LIMIT $page, $pageSize ";
			}
			
			$max_result = $this->_db->sql($select_count)->fetch_element('MAX_RESULT');
			
		} else {
			// if searching over all indexes, limit the results for each index to 10
			foreach ($this->getIndexes() as $title => $classname) {
				$s2[] = "(".$select . " AND class_name = '".$classname."' LIMIT 0, $index_all_limit )";
			}
			$select = implode(" UNION ", $s2);
		}
				
		$this->w->logDebug($select);
		
		$results = $this->_db->sql($select)->fetch_all();
		
		return array($results,$max_result);
	} 
}