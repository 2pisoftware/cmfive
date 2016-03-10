<?php

use \Html\Form\InputField as InputField;

function edit_GET(Web $w) {
	
//	var_dump(strtotime("2016-03-04 11:30"));
//	var_dump(strtotime("2016-03-04 11:30 AM") );
//	var_dump(strtotime("2016-03-04 2:30 PM") );
//	var_dump(strtotime("2016-03-04 23:30") );
//	var_dump(strtotime("2016-03-04 11:30PM") );
//	var_dump(strtotime("2016-03-04 11:30AM") ); // ALl VALID ABOVE
//	var_dump(strtotime("2016-03-04 23:30pm") ); // INVALID
	
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
	
	// If timelog.object_id is required then we must require the search field
	$validation = Timelog::$_validation;
	if (!empty($validation["object_id"])) {
		if (in_array("required", $validation["object_id"])) {
			$validation["search"] = array('required');
		} 
	}
	
	$form = [
		'Timelog' => [
			[
				["Assigned user", $w->Auth->user()->is_admin ? "autocomplete" : "hidden", "user_id", empty($timelog->id) ? $w->Auth->user()->id : $timelog->user_id, $w->Auth->getUsers()]
			],
			[
				["Module", "select", "object_class", $timelog->object_class ? : $tracking_class, $select_indexes],
				["Search", "autocomplete", "search", !empty($timelog->object_id) ? $timelog->object_id : $tracking_id, (!empty($timelog->object_class) || !empty($tracking_class) ? $w->Timelog->getObjects($timelog->object_class ? : $tracking_class) : '')]
			],
            [
				(new InputField(["label" => "Object ID", "type" => "hidden", "name" => "object_id", "id" => "object_id", "value" => $timelog->object_id ? : $tracking_id]))
				//["object id", 'hidden', "object_id", $timelog->object_id ? : $tracking_id],
			],
			[
				['Date', 'date', 'date_start', $timelog->getDateStart()],
			],
			[
				(new InputField())->setLabel("Time Started")->setName("time_start")->setValue($timelog->getTimeStart())
							->setPattern("^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$")
							->setPlaceholder("e.g. 11:30, 11:30am, 23:30, 11:30pm")
							->setRequired("true"),
				(new InputField())->setLabel("Hours Worked")->setName("hours_worked")->setValue($timelog->getHoursWorked())
							->setType("number")->setMin(0)->setMax(23)->setStep(1)->setPlaceHolder("0-23")->setRequired("true"),
				(new InputField())->setLabel("Minutes Worked")->setName("minutes_worked")->setValue($timelog->getMinutesWorked())
							->setType("number")->setMin(0)->setMax(59)->setStep(1)->setPlaceHolder("0-59"),
			],
			[(new InputField())->setLabel("Description")->setName("description")->setValue(!empty($comment) ? $comment->comment : null)]
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
	
	$w->ctx("form", Html::multiColForm($form, "/timelog/edit/" . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), "POST", "Save", "timelog_edit_form", null, null, "_self", true, $validation));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	
	$redirect = $w->request("redirect", '');
	
	// Get and save timelog
	if (empty($_POST['object_class']) || empty($_POST['object_id'])) {
		$w->error('Missing data', $redirect ? : '/timelog');
	}
	
	if (!array_key_exists("date_start", $_POST) || !array_key_exists("time_start", $_POST) || !array_key_exists("hours_worked", $_POST)) {
		$w->error('Missing data', $redirect ? : '/timelog');
	}
	
	// Get start and end date/time
	$time_object = null;
	try {
		$time_object = new DateTime($_POST['date_start'] . ' ' . $_POST['time_start']);
	} catch (Exception $e) {
		$w->error('Invalid start date or time', $redirect ? : '/timelog');
	} 
	
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	$timelog->object_class = $_POST['object_class'];
	$timelog->object_id = $_POST['object_id'];
	$timelog->time_type = !empty($_POST['time_type']) ? $_POST['time_type'] : null;
	
	$timelog->dt_start = $time_object->format('Y-m-d H:i:s');
	$time_object->add(new DateInterval("PT" . intval($_POST['hours_worked']) . "H" . (!empty($_POST['minutes_worked']) ? intval($_POST['minutes_worked']) : 0) . "M0S"));
	$timelog->dt_end = $time_object->format('Y-m-d H:i:s');
//	var_dump($timelog); die();
//	$timelog->dt_start = $w->Timelog->dt2Time($_POST['dt_start']);
//	$timelog->dt_end = $w->Timelog->dt2Time($_POST['dt_end']);
	$timelog->insertOrUpdate();
	
	// Save comment
	$timelog->setComment($_POST['description']);

	$w->msg("<div id='saved_record_id' data-id='".$timelog->id."' >Timelog saved</div>", (!empty($redirect) ? $redirect : "/timelog"));
}
