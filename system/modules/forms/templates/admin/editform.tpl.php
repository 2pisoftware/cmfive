<?php
$frm['Form Details'] = array (
	array(
		array("Title","text","title",$form->title),
	),
	array(
		array("Description","textarea","description",$form->description),
	),
);

echo Html::multiColForm($frm,$w->localUrl("/forms-admin/editform/".$app->id."/".$form->id),"POST"," Save ");

