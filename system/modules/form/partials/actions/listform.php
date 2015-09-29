<?php

function listform_ALL(Web $w, $params) {
	
	$w->ctx("redirect_url", $params['redirect_url']);
	$w->ctx("form", $params['form']);
	$w->ctx("instances", $params['form']->getFormInstances());
	$w->ctx("object", $params['object']);
	
}