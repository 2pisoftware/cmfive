<?php
function editmember_GET(Web &$w) {
	WikiLib::wiki_navigation($w,$wiki,$page);
	$pm = $w->pathMatch("wid","mid");
	$wiki = $w->Wiki->getWikiById($pm['wid']);
	if (!$wiki || !$wiki->isOwner($w->Auth->user()) ) {
		$w->error("No access to this wiki.");
	}
	$mem = $wiki->getUserById($pm['mid']);
	if (!$mem) {
		$mem = new WikiUser($w);
	}
	$w->ctx("wiki",$wiki);
	$w->ctx("mem",$mem);
	$w->setLayout(null);
}

function editmember_POST(&$w) {
	$pm = $w->pathMatch("wid","mid");
	$wiki = $w->Wiki->getWikiById($pm['wid']);
	if (!$wiki || !$wiki->isOwner($w->Auth->user()) ) {
		$w->error("No access to this wiki.");
	}
	$mem = $wiki->getUserById($pm['mid']);
	if (!$mem) {
		$mem = new WikiUser($w);
	}
	$mem->user_id = $w->request("user_id");
	$mem->role = $w->request("role");
	$mem->wiki_id = $wiki->id;
	$mem->insertOrUpdate();
	$w->msg("Member updated.","/wiki/members/".$wiki->id);
}
