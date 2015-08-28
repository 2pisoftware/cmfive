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
	
	$comment = $timelog->getComment();

	$form = [
		'Timelog' => [
			[["Module", "select", "object_class", $timelog->object_class, $select_indexes]],
            [["Search", empty($timelog->object_id) ? "text" : "autocomplete", (empty($timelog->object_id) ? '-' : '') . "search", $timelog->object_id]],
            [["object id", 'hidden', "object_id", $timelog->object_id]],
			[["From", "datetime", "dt_start", $timelog->dt_start ? $w->Timelog->time2Dt($timelog->dt_start) : ""]],
			[["To", "datetime", "dt_end", $timelog->dt_end ? $w->Timelog->time2Dt($timelog->dt_end) : ""]],
			[["Description", "text", "description", !empty($comment) ? $comment->comment : null]]
		]
	];
	
	$additional_form_fields = $w->callHook("timelog", "extra_form_fields", $timelog);
	if (!empty($additional_form_fields[0])) {
		$form['Additional Fields'] = array();
		foreach($additional_form_fields as $form_fields) {
			$form['Additional Fields'][] = $form_fields;
		}
	}

	$w->ctx("form", Html::multiColForm($form, "/timelog/edit/" . $timelog->id, "POST", "Save", "timelog_edit_form"));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	
	// Get and save timelog
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	$timelog->object_class = $_POST['object_class'];
	$timelog->object_id = $_POST['object_id'];
	$timelog->time_type = !empty($_POST['time_type']) ? $_POST['time_type'] : null;
	$timelog->dt_start = $w->Timelog->dt2Time($_POST['dt_start']);
	$timelog->dt_end = $w->Timelog->dt2Time($_POST['dt_end']);
	$timelog->insertOrUpdate();
	
	// Save comment
	$timelog->setComment($_POST['description']);

	// Hook to save any additional form fields
	$w->callHook("timelog", "save_extra_form_fields", $_POST);
	
	$w->msg("Timelog saved", "/timelog");
	
}
