<?php
function members_GET(&$w) {
	WikiLib::wiki_navigation($w,$wiki,$page);
	$pm = $w->pathMatch("id");
	$wiki = $w->Wiki->getWikiById($pm['id']);
	if (!$wiki || !$wiki->isOwner($w->Auth->user())) {
		$w->error("No access to this wiki.");
	}
	$w->ctx("wiki",$wiki);
	$w->ctx("title",$wiki->title." - Members");
}
