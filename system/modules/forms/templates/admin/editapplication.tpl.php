<?php 
$form['Application Details'] = array (
	array(
		array("Title","text","title",$app->title),
	),
	array(
		array("Description","textarea","description",$app->description),
	),
);

echo Html::multiColForm($form,$w->localUrl("/forms-admin/editapplication/".$app->id),"POST"," Save ");

