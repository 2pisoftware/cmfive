<?php
function index_ALL($w) {
	$apps = $w->Forms->getApplications();
	$w->ctx("apps",$apps);
	$w->setTitle("Applications");
}