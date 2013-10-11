<?php
function pagechanges_GET(Web $w) {
	$pm = $w->pathMatch("wid","pagename");
	$wiki = $w->Wiki->getWikiById($pm['wid']);
	if (!$wiki || !$wiki->canRead($w->Auth->user()) ) {
		$w->error("No access to this wiki.");
	}
	$wp = $wiki->getPage($pm['pagename']);
	if (!$wp) {
		$w->error("Page does not exist.","/wiki/index");
	}
        WikiLib::wiki_navigation($w,$wiki,$pm["pagename"]);
	$w->ctx("wiki",$wiki);
	$w->ctx("page",$wp);
}