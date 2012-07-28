<?php
function results_GET(Web $w) {
	
	$q = $w->request('q'); // query

	$idx = $w->request('idx'); // index
	//$p= $w->request('p') ? $w->request('p') : 0; // page number
	//$ps = 20; // page size (after filter)

	if (!$q || strlen($q) < 3) {
		$w->out("Please enter at least 3 characters for searching.");
	} else {
		include_once "sphinx/sphinxapi.php";
		$max = 1000;
		$limit = 1000;
		//$offset = $w->request('of') ? $w->request('of') : 0;

		if (!$idx) {
			$limit = 5;
			$max = 5;
		}

		$cl = new SphinxClient();
		$cl->SetServer($w->moduleConf("search", "host"), $w->moduleConf("search", "port"));
		$cl->SetMatchMode( SPH_MATCH_EXTENDED  );
		$cl->SetLimits(0, $limit, $max);
		$allidx = $w->service('Search')->getSearchIndexes();
		if ($idx) {
			//$cl->SetLimits($p * $ps, ($ps * 2), $max);
			$cl->AddQuery($q,$idx);
		} else {
			foreach ($allidx as $idx) {
				$cl->AddQuery($q,$idx[1]);
			}
		}
		$results = $cl->RunQueries();
		$w->ctx('results',$results);
		$w->ctx('allidx',$allidx);
		//$w->ctx('page',$p);
		//$w->ctx('page_size',$ps);
		$w->ctx('title',"Search results for '".$w->request('q')."'");
	}

}
