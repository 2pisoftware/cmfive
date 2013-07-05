<?php
function viewapplication_GET(Web $w) {
	$p = $w->pathMatch("id");
	
	$app = $w->Forms->getApplication($p['id']);
	
	if (!$app) {
		if (!$w->request("isbox")) {
			$w->error("This application does not exist.","/forms-admin/index");
		} else {
			echo "This application does not exist.";
		}
	}
	$w->ctx("app",$app);
	
	$forms = $app->getForms();
	if ($forms) {
		$lines[] = array("Title","Description","Modified");
		foreach($forms as $form) {
			$line=array();
			$line[]=Html::a("/forms-admin/viewform/".$app->id.'/'.$form->id,$form->title);
			$line[]=$form->description;
			$line[]=formatDateTime($form->dt_modified);
			$lines[]=$line;
		}
		$w->ctx("formstable",Html::table($lines,null,"tablesorter",true));
	}
	
	// need the same for members
	
	$w->setTitle("Edit ".$app->title);
}