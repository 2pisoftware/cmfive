<?php
function viewform_GET(Web $w) {
	$p = $w->pathMatch("application_id","form_id");
	
	$app = $w->Forms->getApplication($p['application_id']);
	if (!$app) {
		$w->error("This application does not exist.","/forms-admin/index");
	}
	
	// TODO check permissions for editing this form!
	
	$form = $p['form_id'] ? $app->getForm($p['form_id']) : null;
	if ($p['form_id'] && !$form) {
		$w->error("No such form.","/forms-admin/index");
	}
	
	$fields = $form->getFields();
	if ($fields) {
		$lines[] = array("Title","Description","Modified");
		foreach($fields as $field) {
			$line=array();
			$line[]=Html::a("/forms-admin/editfield/".$app->id.'/'.$form->id.'/'.$field->id,$field->title);
			$line[]=$field->description;
			$line[]=formatDateTime($field->dt_modified);
			$lines[]=$line;
		}
		$w->ctx("fieldtable",Html::table($lines,null,"tablesorter",true));
	}
	
	$w->ctx("form", $form);
        $w->ctx("app", $app);
}
