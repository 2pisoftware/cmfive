<?php
/**
 * updates or removes favorited item 
 *
 * @author Steve Ryan, steve@2pisystems.com, 2015
 **/
function ajaxEditFavorites_ALL(Web $w) {
	$id = $w->request("id");
    $class=$w->request("class");
    $user = $w->Auth->user();
   	$cmd=$w->request("cmd");
   	
    if (!empty($id) && !empty($class) && !empty($user) && !empty($cmd)){
        if ($cmd=="add") {
			$favorite = new Favorite($w);
			$favorite->object_class=$class;
			$favorite->object_id=$id;
			$favorite->user_id=$user->id;
			$favorite->insertOrUpdate();
			echo $w->Favorite->getFavoriteButton($id,$class);
		} else if ($cmd=="remove") {
			$favorite = $w->Favorite->getDataByObject($id,$class);
			if (get_class($favorite)=="Favorite" && $favorite->id>0) {
				$favorite->delete();
			}
			echo $w->Favorite->getFavoriteButton($id,$class);
		} else {
			echo "Invalid request";
		}
	} else {
		echo "Invalid request";
	}
}
