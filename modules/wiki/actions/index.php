<?php
function index_ALL(Web $w) {
	WikiLib::wiki_navigation($w,$wiki,$page);
	$w->ctx("wikis",$w->Wiki->getWikis());
}