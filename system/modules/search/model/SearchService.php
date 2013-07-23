<?php
class SearchService extends DbService {

	/**
	 * Returns an array of search indeces that the currently logged in user
	 * has access to. This uses the global module configuration and auth system
	 * to check.
	 * 
	 * @return array
	 */
	public function getIndices() {
		
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
	public function getResults($query, $index = null, $page = null, $pageSize = 25) {
		
		// sanity check
		if (empty($query) || strlen($query) < 3) {
			return null;
		}
		
		
	} 
}