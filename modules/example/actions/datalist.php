<?php
/*
 * @author Robert Lockerbie <robert@lockerbie.id.au> June, 2016
 */
function datalist_ALL(Web $w) {
	$w->enqueueStyle(array("name" => "datalist.css", "uri" => "/system/templates/css/datalist.css", "weight" => 990));
	$w->enqueueScript(array("name" => "moment.js", "uri" => "/system/templates/js/moment.js", "weight" => 990));
	$datalist = array(
		'ExampleData' => array(
			'title' => 'Example Data',
			'url' => '/example-data/',
			'filters' => array(
				'keyword_field' => 'title',
			),
			'fields' => array(
				'title' => array(
					'title' => 'Title',
					'sortable' => true
				),
				'data' => array(
					'title' => 'Data'
				),
				'd_date_field' => array(
					'title' => 'Date Field'
				),
				'dt_datetime_field' => array(
					'title' => 'Date Time Field'
				),
				't_time_field' => array(
					'title' => 'Time Field'
				),
			),
			'buttons' => array(
				'/example/edit/' => array(
					'title' => 'Edit'
				)
			),
		)
	);
	$w->ctx('datalist', Html::datalist($datalist));
}