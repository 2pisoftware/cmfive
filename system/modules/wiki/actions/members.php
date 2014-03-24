<?php
function members_GET(&$w) {
	$pm = $w->pathMatch("id");
	$wiki = $w->Wiki->getWikiById($pm['id']);
	if (!$wiki || !$wiki->isOwner($w->Auth->user())) {
		$w->error("No access to this wiki.");
	}
	$w->ctx("wiki",$wiki);
	$w->Wiki->navigation($w,$wiki,"Members");
}
