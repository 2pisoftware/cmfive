<?php

function listtimelog_ALL($w, $params) {
	if (empty($params['object_class']) || empty($params['object_id'])) {
		return;
	}
	
	$timelogs = $w->Timelog->getTimelogsForObjectByClassAndId($params['object_class'], $params['object_id']);
	if (!empty($timelogs)) {
		uasort($timelogs, function($a, $b) {
			return $a->dt_start - $b->dt_end;
		});
	
		$total = array_reduce($timelogs, function($carry, $timelog) {
			return $carry += $timelog->getDuration();
		});
	}
	
	$w->ctx("total", !empty($total) ? $total : 0);
	$w->ctx("class", $params['object_class']);
	$w->ctx("id", $params['object_id']);
	$w->ctx("redirect", !empty($params['redirect']) ? $params['redirect'] : "");
	$w->ctx("timelogs", $timelogs);
}