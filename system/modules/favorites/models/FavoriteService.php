<?php
/**
 * Service class for Favorite
 * 
 * @author Steve Ryan, steve@2pisystems.com, 2015
 */
class FavoriteService extends DbService {
	
	/** 
	 * @return an array of all undeleted ExampleData records from the database
	 */
	function getAllData() {
		return $this->getObjects("Favorite",array("is_deleted" => 0));
	}
	
	function getDataByUser($user_id) {
		return $this->getObjects("Favorite",array("is_deleted" => 0,"user_id"=>$user_id));
	}
	
	function getDataByUserAndClass($user_id,$class) {
		return $this->getObjects("Favorite",array("is_deleted" => 0,"user_id"=>$user_id,"object_class"=>$class));
	}
	
	function getDataByObject($object_id,$class) {
		$objects=$this->getObjects("Favorite",array("is_deleted" => 0,"object_id"=>$object_id,"object_class"=>$class));
		if (count($objects)>0) { 	
			return $objects[0];
		}
	}
	
	
	/**
	 * @param integer $id
	 * @return an ExampleData object for this id
	 */
	function getDataForId($id) {
		return $this->getObject("Favorite",$id);
	}
	
	/**
	* inserts favorite button
	* $w->Faorite->getFavoriteButton($id,$class);
	**/
	function getFavoriteButton($id,$class) {
		$response='';
		$user = $this->w->Auth->user();
		if (!empty($id) && !empty($class) && !empty($user)){
			$response.='<script src="/system/modules/favorites/assets/js/favoriteButton.js"></script>';
			$favorite = $this->w->Favorite->getDataByObject($id,$class);
			$url='/favorites/ajaxEditFavorites?class='.$class;
			//$.load(function(result) {console.log([\'loaded\',result]);$(this).replace(result);});
			if (!empty($favorite)) {
				$response.='<a   class="flagfavorite flagfavoriteon" title="Remove favorite" href="'.$url.'&cmd=remove&id='.$id.'" ><img src="/system/modules/favorites/assets/img/favorite_on_small.png" ></a>';
			} else {
				$response.='<a class="flagfavorite flagfavoriteoff" title="Add as favorite" href="'.$url.'&cmd=add&id='.$id.'" ><img src="/system/modules/favorites/assets/img/favorite_off_small.png" ></a>';
			}
		} else {
			return ''; 
		}
		return $response;
	}

}
