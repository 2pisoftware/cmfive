<?php
function wikichanges_GET(Web &$w) {
	WikiLib::wiki_navigation($w,$wiki,$page);
	$pm = $w->pathMatch("wid","pagename");
	$wiki = $w->Wiki->getWikiById($pm['wid']);
	if (!$wiki || !$wiki->canRead($w->Auth->user()) ) {
		$w->error("No access to this wiki.");
	}
	$w->ctx("wiki",$wiki);
}
