<?php
function results_GET(Web $w) {
	
	$q = $w->request('q'); // query
	$idx = $w->request('idx'); // index

	if ($q && strlen($q) >= 3) {
		$results = $w->Search->getResults($q, $idx);
		$w->ctx('results',$results);
		$w->ctx('title',"Search results for '".$w->request('q')."'");
	} else {
		$w->out("Please enter at least 3 characters for searching.");
	}
}
