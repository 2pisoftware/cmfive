<?php
function read_GET(Web $w){
	$w->Inbox->navigation($w,"Read Messages");
	$p = $w->pathMatch('num');
	$num = $p['num'];
	$num ? $num : $num = 1;
	$read = $w->Inbox->getMessages($num-1,40,$w->Auth->user()->id,0);
	$read_total = $w->Inbox->getReadMessageCount($w->Auth->user()->id);
	$read_total ? $read_total = $read_total['COUNT(*)'] : "";
	$w->ctx('pgnum',$num);
	$w->ctx("readtotal",$read_total);
	$w->ctx("read",$read);
}