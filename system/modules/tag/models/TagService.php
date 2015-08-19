<?php
class TagService extends DbService {
	public function getTag($id = null){
		if (!empty($id)) {
			return $this->getObject("Tag", array("id" => intval($id)));
		}
		return null;
	}
	public function getTagsByObject($object_id, $class) {
		$object=$this->getObjects("Tag",array("is_deleted" => 0,"obj_id"=>$object_id,"obj_class"=>$class));
		if( !empty($object) ) {
			return $object;
		}
	}
	public function removeTag($object_id, $obj_class, $tagText) {
		//No id needed as the other fields act as our unique identifiers
		if( !empty($object_id) && !empty($obj_class) ) {
			$tag = $this->getObject("Tag", array("obj_class" => $obj_class, "obj_id" => $object_id, 'tag' => $tagText));
			if(!empty($tag)) {
				$tag->delete();
			}
		}
	}
	public function getAllTags($returnObjects=false) {
		//@TODO: Is there a way to do this without raw SQL?
		//Loads a list of all tags that were ever created
		//How to really delete a tag...?
		$tags = $this->_db->sql('SELECT id,tag,tag_color FROM tag WHERE 1 GROUP BY tag ORDER BY tag')->fetch_all();
		if($returnObjects) {
			if(!empty($tags)) {
				$objects = $this->getObjectsFromRows('Tag', $tags, true);
				return $objects;
			}
		}
		$tagList = array();
		foreach($tags as $tag) {
			$tagList[$tag['id']] = $tag;
		}
		return $tagList;
	}
	/**
	 * An easy way to save a tag against an object
	 * @param <Int> $object_id
	 * @param <String> $obj_class
	 * @param <String> $tagText
	 * @param <String> $color
	 */
	public function addTag($object_id, $obj_class, $tagText, $color='') {
		//First look for existing deleted record and restore it if found
		//Otherwise create a new record...
		$tagLookup = $this->getObject("Tag", array("obj_class" => $obj_class, "obj_id" => $object_id, 'tag' => $tagText));
		if(!empty($tagLookup)) {
			$tagLookup->is_deleted = 0;
			$tagLookup->update();
		} else {
			$tag = new Tag($this->w);
			$tag->obj_class = $obj_class;
			$tag->obj_id = $object_id;
			$user = $this->w->Auth->user();
			$tag->user_id = $user->id;
			$tag->tag_color = $color;
			$tag->tag = trim(strip_tags($tagText));
			$tag->insert();
		}
	}
	
	public function getTagButton($id, $class) {
		$buf = '';
		$user = $this->w->Auth->user();
		if( !empty($user) ) {
			//Check roles access
			//Admin gets to add new tags globally
			//User can attach an existing tag
			//Different scripts handle this functionality - more checks done in action
			if( $user->hasRole("tag_admin") ) {
				$buf .= '<script src="/system/modules/tag/assets/js/tagButtonAdmin.js"></script>';
			} else if( $user->hasRole("tag_user") ) {
				$buf .= '<script src="/system/modules/tag/assets/js/tagButton.js"></script>';
			}
		}
		if( !empty($id) && !empty($class) ) {
			//Load all tags for this object
			$tags = $this->getTagsByObject($id, $class);
			$url = '/tag/ajaxTag/?class='.$class.'&id='.$id;
			//Build list of tags
			$buf .= '<span class="tag_list" data-url="'.$url.'">';
			if( empty($tags) ) {
				$buf .= '<span class="label radius secondary no_tags tag_selection"><span class="fi-price-tag">No tag</span></span> ';
			} else {
				$buf .= '<span style="display:none;" class="label radius secondary no_tags tag_selection"><span class="fi-price-tag">No tag</span></span> ';
				foreach($tags as $tag) {
					$buf .= '<span data-tag="'.$tag->tag.'" class="label radius secondary tag_selection"><span '.(!empty($tag->tag_color) ? 'style="color:'.$tag->tag_color.'"' : '').' class="fi-price-tag">'.$tag->tag.'</span></span> ';
				}
			}
			//Build tag dialog popup
			//This is empty on load and is dynamically filled
			$tagDialogId = 'tag_list_'.$class.$id;
			$buf .= '<div class="tag_list_dialog" id="'.$tagDialogId.'">
				<div class="tag_list_modal">
					<div class="tag_list_header">Available tags <span class="fi-x hide_tag_list"></span><div><input type="text" placeholder="Filter tags" class="search_tags" /></div></div>
					<div class="tag_list_body"></div>
				</div>
			</div></span>';
		}
		return $buf;
	}
}