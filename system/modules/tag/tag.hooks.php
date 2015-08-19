<?php

function tag_core_dbobject_add_to_index($w, $obj) {
	$tags = $w->Tag->getTagsByObject($obj->id, get_class($obj));
	$words = array();
	if(!empty($tags)) {
		foreach($tags as $tag) {
			$words[] = 'unitag'.$tag->tag;
		}
		return $words;
	}
}