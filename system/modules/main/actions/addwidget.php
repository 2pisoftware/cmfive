<?php

function addwidget_GET(Web $w) {

	$p = $w->pathMatch("module");
	$module = $p["module"];

	$form = array("Add a widget" =>
		array(
			array(array("Add widget for", "select", "destination_module", $module, $w->modules())),
			array(array("Source module", "select", "source_module", null, $w->modules())),
			array(array("Widget Name", "select", "widget_name", null, array()))
		)
	);

	$w->ctx("widgetform", Html::multiColForm($form, "/main/addwidget/{$module}", "POST", "Add"));
}

function addwidget_POST(Web $w) {

	$p = $w->pathMatch("module");
	$module = $p["module"];

	$widget = $w->Widget->getWidget($_POST["destination_module"], $_POST["source_module"], $_POST["widget_name"]);
	if (null !== $widget) {
		$w->error("This entry already exists!", "/{$module}/index");
	}

	$widget = new WidgetConfig($w);
	$widget->fill($_POST);
	$widget->user_id = $w->Auth->user()->id;
	$response = $widget->insert();

	if ($response === true) {
		$w->msg("Widget Added", "/{$module}/index");
	} else {
		$w->error("Could not add widget", "/{$module}/index");
	}
}