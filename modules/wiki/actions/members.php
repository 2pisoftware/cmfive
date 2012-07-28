<?php
function members_GET(&$w) {
	wiki_navigation($w,$wiki,$page);
	$pm = $w->pathMatch("id");
	$wiki = $w->Wiki->getWikiById($pm['id']);
	if (!$wiki || !$wiki->isOwner($w->auth->user())) {
		$w->error("No access to this wiki.");
	}
	$w->ctx("wiki",$wiki);
	$w->ctx("title",$wiki->title." - Members");
}
