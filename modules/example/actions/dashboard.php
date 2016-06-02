<?php
/*
 * @author Robert Lockerbie <robert@lockerbie.id.au> June, 2016
 */
function dashboard_ALL(Web $w) {
	$w->enqueueStyle(array("name" => "dashboard.css", "uri" => "/system/templates/css/dashboard.css", "weight" => 990));
	$dashboard = array(
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
			),
		)
	);
	$w->ctx('dashboard', Html::dashboard($dashboard));
}