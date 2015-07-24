<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	
	$indexes = $w->search->getIndexes();
    $select_indexes = [];
    if (!empty($indexes)) {
        foreach($indexes as $friendly_name => $search_name) {
            $select_indexes[] = array($friendly_name, $search_name);
        }
    }

	$form = [
		'Timelog' => [
			[["Module", "select", "module", null, $select_indexes]],
            [["Search", "text", "-search"]],
            [["object id", 'hidden', "object_id", $timelog->object_id]],
			[["From", "datetime", "dt_start", $timelog->dt_start ? $w->Timelog->time2Dt($timelog->dt_start) : ""]],
			[["To", "datetime", "dt_end", $timelog->dt_end ? $w->Timelog->time2Dt($timelog->dt_end) : ""]],
			[["Description", "text", "description", $timelog->getComment()]]
		]
	];
	
	$w->ctx("form", Html::multiColForm($form, "/timelog/edit/" . $timelog->id));
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	$timelog->object_class = $_POST['object_class'];
	$timelog->object_id = $_POST['object_id'];
	$timelog->dt_start = $w->Timelog->dt2Time($_POST['dt_start']);
	$timelog->dt_end = $w->Timelog->dt2Time($_POST['dt_end']);
	$timelog->insertOrUpdate();
	
	$timelog->setComment($_POST['description']);
	
	$w->msg("Timelog saved", "/timelog");
	
}
