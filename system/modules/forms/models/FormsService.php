<?php
class FormsService extends DbService {
	
	function getApplication($idOrSlug) {
		if (!$idOrSlug) return null;
		
		if (is_numeric($idOrSlug)) {
			return $this->getObject("FormsApplication", $idOrSlug);
		} else {
			return $this->getObject("FormsApplication", array("slug",$idOrSlug));
		}
	}
	
	function getApplications($include_deleted=false) {		
		if (!$include_deleted) {
			$where['is_deleted'] = "0";
		}
		return $this->getObjects("FormsApplication",$where);
	}
}