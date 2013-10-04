<?php
function wikichanges_GET(Web &$w) {
	$pm = $w->pathMatch("wid","pagename");
	$wiki = $w->Wiki->getWikiById($pm['wid']);
	if (!$wiki || !$wiki->canRead($w->Auth->user()) ) {
		$w->error("No access to this wiki.");
	}
        WikiLib::wiki_navigation($w, $wiki, $pm['pagename']);
	$w->ctx("wiki",$wiki);
}
