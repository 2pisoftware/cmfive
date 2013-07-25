<?php
function results_GET(Web $w) {
	
	$q = $w->request('q'); // query
	$idx = $w->request('idx'); // index
	$p = $w->request('p'); // page
	$ps = $w->request('ps'); // pageSize
	
	if ($q && strlen($q) >= 3) {
		$results = $w->Search->getResults($q, $idx,$p,$ps);
		$w->ctx('results',$results[0]);
		$w->ctx('max_results',$results[1]);
		$w->ctx('page',$p);
		$w->ctx('page_size',$ps);
		$w->ctx('title',"Search results for '".$w->request('q')."'");
	} else {
		$w->out("Please enter at least 3 characters for searching.");
	}
}
