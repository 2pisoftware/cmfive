<?php
function edit_GET(Web &$w){
	WikiLib::wiki_navigation($w,$wiki,$page);
	$pm = $w->pathMatch("wikiname","pagename");
	try {
		$wiki = $w->Wiki->getWikiByName($pm['wikiname']);
	} catch (WikiException $ex) {
		$w->error($ex->getMessage(),"/wiki/index");
	}
	$wp = $wiki->getPage($pm['pagename']);
	if (!$wp) {
		$w->error("Page does not exist.","/wiki/index");
	}
	$w->ctx("wiki",$wiki);
	$w->ctx("page",$wp);
	$w->ctx("attachments",$w->service("File")->getAttachments($wp));
	$w->ctx("title",$wiki->title." - ".$wp->name);
}

function edit_POST(Web &$w) {
	$pm = $w->pathMatch("wikiname","pagename");
	$wiki = $w->Wiki->getWikiByName($pm['wikiname']);
	if (!$wiki) {
		$w->error("Wiki does not exist.");
	}
	$wp = $wiki->getPage($pm['pagename']);
	if (!$wp) {
		$w->error("Page does not exist.");
	}
	$wiki->updatePage($pm['pagename'],$w->request("body"));
	$w->msg("Page updated.","/wiki/view/".$pm['wikiname']."/".$pm['pagename']);
}
