<?php

function removewidget_ALL(Web $w) {

	$p = $w->pathMatch("origin", "source", "widget");

	$widget = $w->Widget->getWidget($p["origin"], $p["source"], $p["widget"]);
	if ($widget == null) {
		$w->error("Widget not found", "/{$p['origin']}");
	}

	$widget->delete();

	$w->msg("Widget removed", "/{$p['origin']}");

}