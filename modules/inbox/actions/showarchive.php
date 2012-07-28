<?php
function showarchive_ALL(Web $w){
	inbox_navigation($w,"Archive");

	$p = $w->pathMatch('num');
	$num = $p['num'];
	$num ? $num : $num = 1;
	$new_arch = $w->Inbox->getMessages($num-1,40,$w->Auth->user()->id,0,1);
	//$arch = $w->Inbox->getMessages($num-1,40,$w->Auth->user()->id,0,1);
	$arch_count = $w->Inbox->getArchCount($w->Auth->user()->id);
	//$read_total = $w->Inbox->getReadMessageCount($w->Auth->user()->id);
	//$read_total ? $read_total = $read_total['COUNT(*)'] : "";
	$w->ctx('pgnum',$num);
	$w->ctx("readtotal",$arch_count);
	$w->ctx("arch",$arch);
	$w->ctx("new_arch",$new_arch);
}