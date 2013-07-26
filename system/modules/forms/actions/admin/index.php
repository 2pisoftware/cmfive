<?php
function index_GET(Web $w) {
	$apps = $w->Forms->getApplications();
	
	$table[]=array("Application","Description","Modified");
	if ($apps) {
		foreach ($apps as $app) {
			$line = array();
			$line[]=Html::a($w->localUrl("/forms-admin/viewapplication/".$app->id),$app->title);
			$line[]=$app->description;
			$line[]=formatDateTime($app->dt_modified);
			$table[]=$line;
		}
	}
	$w->ctx("table",Html::table($table,null,"tablesorter",true));
	$w->setTitle("Edit Applications");
}