<?php
/*
 *  Display Edit and Testdata forms for Templates
 */

function edit_GET(Web $w) {
	AdminLib::navigation($w,"Templates");
	$p = $w->pathMatch("id");
	
	$t = $w->Template->getTemplate($p['id']);
	$t = $t ? $t : new Template($w);
	
	$newForm = array();
	$newForm["Template Details"] = array(
		array(array("Title", "text", "title",$t->title),
				array("Active", "checkbox", "is_active",$t->is_active)),
		array(array("Module", "text", "module",$t->module),
				array("Category", "text", "category",$t->category)));
	$newForm['Description'] = array(
		array(array("", "textarea", "description",$t->description)),
	);

	$w->ctx("editdetailsform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/'.$t->id)));
	
	$newForm = array();
	$newForm["Template Title"] = array(
			array(array("", "textarea", "template_title",$t->template_title,100,1))
	);
	$newForm["Template Body"] = array(
			array(array("", "textarea", "template_body",$t->template_body,100,30))
	);

	$w->ctx("templateform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/'.$t->id)));
	
	$newForm = array();
	$newForm["Title Data"] = array(
			array(array("", "textarea", "test_title_json",$t->test_title_json,100,5))
	);
	$newForm["Body Data"] = array(
			array(array("", "textarea", "test_body_json",$t->test_body_json,100,20))
	);
	
	$w->ctx("testdataform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/'.$t->id)));
	
	$w->ctx("testtitle",$t->testTitle());
	$w->ctx("testbody",$t->testBody());
		
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	$t = $w->Template->getTemplate($p['id']);
	$t = $t ? $t : new Template($w);
	$t->fill($_POST);
	$t->insertOrUpdate();
	$w->msg("Template saved", "/admin-templates/edit/".$t->id);	
}