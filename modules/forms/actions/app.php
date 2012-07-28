<?php
function app_GET(Web $w) {
	$p = $w->pathMatch("slug");
	
	$app = $w->Forms->getApplication($p['slug']);
	
	$w->ctx("app",$app);
	$w->setTitle($app->title);
}