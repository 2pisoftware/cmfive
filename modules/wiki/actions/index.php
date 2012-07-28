<?php
function index_ALL(Web $w) {
	wiki_navigation($w,$wiki,$page);
	$w->ctx("wikis",$w->Wiki->getWikis());
}