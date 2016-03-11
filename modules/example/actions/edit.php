<?php
/**
 * Display an edit form for either creating a new
 * record for ExampleData or edit an existing form.
 * 
 * Url:
 * 
 * /example/edit/{id}
 * 
 * @param Web $w
 */
function edit_GET(Web $w) {
	// parse the url into parameters
	$p = $w->pathMatch("id");
	
	// create either a new or existing object
	if (isset($p['id'])) {
		$data = $w->Example->getDataForId($p['id']);
	} else {
		$data = new ExampleData($w);
	}
	// text,textarea,rte,file,multifile,select,multiSelect,radio,checkbox,password,email,time,hidden,date,datetime,time,autocomplete
	// text_field,select_field,autocomplete_field,multiSelect_field,radio_field,checkbox_field,password_field,email_field,time_field,hidden_field,date_field,datetime_field,time_field,textarea_field,rte_field,file_field,multifile_field
	
	/*	*/			  
	// create the edit form
	$formDefinition=array(
		array(__("Edit Example Data"),"section"),
		array(__("Title"),"text","title", $data->title),
		array(__("Data"),"textarea","data",$data->data),
		array(__("Check"),"checkbox","example_checkbox",$data->example_checkbox),
		array(__("select field"), "select", "select_field", $data->select_field, ['fred','john']) ,
		array(__("autocomplete field"), "autocomplete", "autocomplete_field", $data->autocomplete_field, ['fred','john']) ,
		array(__("multiselect field"), "multiselect", "multiselect_field", $data->multiselect_field, ['fred','john']) ,
		array(__("radio field"), "radio", "radio_field", $data->radio_field, 'Radio NO') ,
		array(__("password field"),"password","password_field", $data->password_field),
		array(__("email field"),"email","email", $data->email_field),
		array(__("hidden field"),"hidden","hidden", $data->hidden_field),
		array(__("date field"), "date", "d_date_field", formatDate($data->d_date_field)),
		array(__("datetime field"), "datetime", "dt_datetime_field", formatDate($data->dt_datetime_field)),
		array(__("time field", "time"), "t_time_field", formatDate($data->t_time_field,'H:i:s')),
		array(__("file field","file"),"file_field", $data->file_field),
		array(__("multifile field"),"multifile","multifile_field", $data->multifile_field )
	);
	$f = Html::form($formDefinition,
		$w->localUrl("/example/edit/".$p['id'])
		,"POST"
		,__(" Save ")
	);
	
	// circumvent the template and print straight into the layout
	$w->out($f);
}

/**
 * Receive post data from ExampleData edit form.
 * 
 * Url:
 * 
 * /example/edit/{id}
 * 
 * @param Web $w
 */
function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	if (isset($p['id'])) {
		$data = $w->Example->getDataForId($p['id']);
	} else {
		$data = new ExampleData($w);
	}
	
	$data->fill($_POST);
        
    $data->example_checkbox = !empty($_POST['example_checkbox']) ? '1' : '0';
	// fill in validation step!
	
	$data->insertOrUpdate();
	
	// go back to the list view
	$w->msg(__("ExampleData updated"),"example/index");
}
