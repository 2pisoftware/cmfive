<?php
function index_GET($w){
	AdminLib::navigation($w,"Templates");
	$w->ctx("templates_list",$w->Templates->findTemplates());
}