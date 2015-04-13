<?php
/*
 * Partial action that lists leave applications potentially filtered by employee or date range or status
 * @author Steve Ryan steve@2pisoftware.com 2015
 */
function listfavorite_ALL(Web $w,$params) {
	if (!empty($w->Auth->user()))  {
		$results = $w->Favorite->getDataByUser($w->Auth->user()->id );
		$favoritesCategorised=array();
		$service=new DBService($w);
		if (!empty($results)) {
			foreach ($results as $k => $favorite) {
				if (!array_key_exists($favorite->object_class,$favoritesCategorised)) $favoritesCategorised[$favorite->object_class]=array();
				$realObject=$service->getObject($favorite->object_class,$favorite->object_id);
				if (!empty($realObject))  {
					$templateData=array();
					$templateData['title']=$realObject->printSearchTitle();
					$templateData['url']=$realObject->printSearchUrl();
					$templateData['listing']=$realObject->printSearchListing();
					if ($realObject->canList($w->Auth->user()) && $realObject->canView($w->Auth->user())) array_push($favoritesCategorised[$favorite->object_class],$templateData);
				}
			}
		}
		$w->ctx('categorisedFavorites',$favoritesCategorised);
	}
}
