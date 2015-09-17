<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	
	$redirect = $w->request("redirect", '');
	
	$indexes = $w->search->getIndexes();
    $select_indexes = [];
    if (!empty($indexes)) {
        foreach($indexes as $friendly_name => $search_name) {
            $select_indexes[] = array($friendly_name, $search_name);
        }
    }
	
	$comment = $timelog->getComment();
	
	$tracking_class = $w->request("class");
	$tracking_id = $w->request("id");
	
	$form = [
		'Timelog' => [
			[["Module", "select", "object_class", $timelog->object_class ? : $tracking_class, $select_indexes]],
            [["Search", empty($timelog->object_id) && empty($tracking_id) ? "text" : "autocomplete", (empty($timelog->object_id) && empty($tracking_id) ? '-' : '') . "search", !empty($timelog->object_id) ? $timelog->object_id : $tracking_id, (!empty($timelog->object_class) || !empty($tracking_class) ? $w->Timelog->getObjects($timelog->object_class ? : $tracking_class) : '')]],
            [["object id", 'hidden', "object_id", $timelog->object_id ? : $tracking_id]],
			[["From", "datetime", "dt_start", formatDateTime($timelog->dt_start)]],
			[["To", "datetime", "dt_end", formatDateTime($timelog->dt_end)]],
			[["Description", "text", "description", !empty($comment) ? $comment->comment : null]]
		]
	];
	
	$object = $w->Timelog->getObject($timelog->object_class ? : $tracking_class, $timelog->object_id ? : $tracking_id);
	
	// Hook relies on knowing the timelogs time_type record, but also the object, so we give the time_type to object
	if (!empty($object->id) && !empty($timelog->id)) {
		$object->time_type = $timelog->time_type;
	}
	
	if (!empty($object)) {
		$additional_form_fields = $w->callHook("timelog", "type_options_for_" . get_class($object), $object);
		if (!empty($additional_form_fields[0])) {
			$form['Additional Fields'] = array();
			foreach($additional_form_fields as $form_fields) {
				$form['Additional Fields'][] = $form_fields;
			}
		}
	}
	
	$w->ctx("form", Html::multiColForm($form, "/timelog/edit/" . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), "POST", "Save", "timelog_edit_form"));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	
	$redirect = $w->request("redirect", '');
	
	// Get and save timelog
	if (empty($_POST['object_class']) || empty($_POST['object_id']) || empty($_POST['dt_start']) || empty($_POST['dt_end'])) {
		$w->error('Missing data', '/timelog');
	}
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	$timelog->object_class = $_POST['object_class'];
	$timelog->object_id = $_POST['object_id'];
	$timelog->time_type = !empty($_POST['time_type']) ? $_POST['time_type'] : null;
	$timelog->dt_start = $w->Timelog->dt2Time($_POST['dt_start']);
	$timelog->dt_end = $w->Timelog->dt2Time($_POST['dt_end']);
	$timelog->user_id = $w->Auth->user()->id;
	$timelog->insertOrUpdate();
	
	// Save comment
	$timelog->setComment($_POST['description']);

	$w->msg("Timelog saved", (!empty($redirect) ? $redirect : "/timelog"));
}
