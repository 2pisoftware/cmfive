<?php
use Sabre\DAV;

/**
 * Service for webdav
 */

class INodeService extends DBService {
		/*****************************
	 * Generate menu entries for navigation
	 * @return array 
	*****************************/
	public function navigation(Web $w,$object, $path) {
		$nav = array();
		$w->menuLink("webdav/index/fred", "Attachments", $nav);
		$w->ctx("navigation", $nav);
		return $nav;
	}
	
	 
	

}
