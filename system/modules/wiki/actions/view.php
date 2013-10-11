<?php
function view_GET(Web &$w) {
	$pm = $w->pathMatch("wikiname","pagename");
	$wiki = $w->Wiki->getWikiByName($pm['wikiname']);
	if (!$wiki) {
		$w->error("Wiki does not exist.");
	}
        
	$wp = $wiki->getPage($pm['pagename']);
	if (!$wp) {
		$wp = $wiki->addPage($pm['pagename'],"New Page.");
	}
	if ($pm['pagename'] == "HomePage") {
		$_SESSION['wikicrumbs'][$pm['wikiname']] = array();
	} else {
		$_SESSION['wikicrumbs'][$pm['wikiname']][$pm['pagename']] = 1;
	}
        
        WikiLib::wiki_navigation($w,$wiki,$pm["pagename"]);
	$w->ctx("body",WikiLib::wiki_format_creole($wiki,$wp));
	$w->ctx("wiki",$wiki);
	$w->ctx("page",$wp);
	$w->ctx("attachments",$w->service("File")->getAttachments($wp));
	$w->ctx("title",$wiki->title." - ".$wp->name);
}