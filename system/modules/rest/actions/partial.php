<?php
function partial_GET(Web &$w) {
	$w->setLayout(null);
	// LIMIT 40 TOKENS IN SEARCH QUERY
	$p=$w->pathMatch('module','partial',"token1","token2","token3","token4","token5","token6","token7","token8","token9","token10","token11","token12","token13","token14","token15","token16","token17","token18","token19","token20","token21","token22","token23","token24","token25","token26","token27","token28","token29","token30","token31","token32","token33","token34","token35","token36","token37","token38","token39","token40");
	$token = $w->request("token");
	$count=0;
	foreach($p as $pk=>$pv) {
		if ($count>1) $where[]=$pv;
		$count++;
	} 
	$w->ctx('params',['where'=>$where]);
	$w->ctx('module',$p['module']);
	$w->ctx('partial',$p['partial']);
}
