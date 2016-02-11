<?php
/**
 * Partial action that lists favorite objects
 * @author Steve Ryan steve@2pisoftware.com 2015
 */
function listfavorite_ALL(Web $w,$params) {
	if (!empty($params['results']) && is_array($params['results']->success)) {
		$user = $w->Auth->user();
		if (!empty($user))  {
			// convert rest results for template
			$favoritesCategorised=array();
			$service=new DBService($w);
			
			foreach ($params['results']->success as $v) {
				$result=get_object_vars($v);
				print_r(['RES',$result]);
				$favorite= new Favorite($w);
				$favorite->fill($result);
				if (!array_key_exists($favorite->object_class,$favoritesCategorised)) $favoritesCategorised[$favorite->object_class]=array();
				$realObject=$service->getObject($favorite->object_class,$favorite->object_id);
				if (!empty($realObject))  {
					$templateData=array();
					$templateData['title']=$realObject->printSearchTitle();
					$templateData['url']=$realObject->printSearchUrl();
					$templateData['listing']=$realObject->printSearchListing();
					if ($realObject->canList($user) && $realObject->canView($user)) array_push($favoritesCategorised[$favorite->object_class],$templateData);
				}
			}
			$w->ctx('categorisedFavorites',$favoritesCategorised);
		}
	} else {
		$user = $w->Auth->user();
		if (!empty($user))  {
			$results = $w->Favorite->getFavoritesForUser($user->id );
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
						if ($realObject->canList($user) && $realObject->canView($user)) array_push($favoritesCategorised[$favorite->object_class],$templateData);
					}
				}
			}
			$w->ctx('categorisedFavorites',$favoritesCategorised);
		}
	}
}
