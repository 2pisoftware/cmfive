<?php
function results_GET(Web $w) {
	
	$q = $w->request('q'); // query
	$idx = $w->request('idx'); // index
	$p = $w->request('p'); // page
	$ps = $w->request('ps'); // pageSize	
	$tr = $w->request('tr'); // total results
	
	if ($q && strlen($q) >= 3) {
		$results = $w->Search->getResults($q, $idx,$p,$ps);
		$w->ctx('results',$results[0]);
		$w->ctx('max_results',$results[1]);

		// Work out number of pages
		$numpages = ($ps >= $results[1] ? 1 : ceil($results[1]/$ps));

		$w->ctx('page',$p);
		$w->ctx('page_size',$ps);
		$w->ctx('title',"Search results for '".$w->request('q')."' " . ($numpages > 1 ? " (Page $p of $numpages)" : ""));

		// Print pagination if showing single index
		if ($results[1] > 0 and !empty($idx)){
			$numpages = ($ps >= $results[1] ? 1 : ceil($results[1]/$ps));
			$w->ctx('pagination', Html::pagination($p, $numpages, $ps, count($results), "/search/results?q={$q}&idx={$idx}"));
		}
	} else {
		$w->out("Please enter at least 3 characters for searching.");
	}
}
