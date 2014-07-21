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
	
	public function navigation(Web $w, $title = null, $prenav=null) {
		if ($title) {
			$w->ctx("title",$title);
		}
		$nav = $prenav ? $prenav : array();
		if ($w->Auth->hasRole("forms_admin")) {
			$w->menuLink("forms-admin/index","Edit Applications", $nav);
		}
		$apps = $w->Forms->getApplications();
		if ($apps) {
			foreach ($apps as $app) {
				$w->menuLink("forms/app/".$app->slug,$app->title,$nav);
			}
		}
		$w->ctx("navigation", $nav);
		return $nav;
	}
	
}