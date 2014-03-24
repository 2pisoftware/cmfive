<?php
function index_GET($w){
	$w->Admin->navigation($w,"Templates");
	$w->ctx("templates_list",$w->Template->findTemplates());
}