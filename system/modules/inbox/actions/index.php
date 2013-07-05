<?php
function index_GET(Web &$w) {
	InboxLib::inbox_navigation($w,"");
	$p = $w->pathMatch('num');
	$num = $p['num'];
	$num ? $num : $num = 1;
	$new_total = $w->Inbox->getNewMessageCount($w->Auth->user()->id);
	$new_total ? $new_total = $new_total['COUNT(*)'] : "";
	$new = $w->Inbox->getMessages($num-1,40,$w->Auth->user()->id,1);
	$w->ctx('pgnum',$num);
	$w->ctx("newtotal",$new_total);
	$w->ctx("new",$new);
}