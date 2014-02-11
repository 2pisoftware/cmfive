<?php

function listwidgets_ALL(Web $w, $params) {
	$module = null;
	if (!empty($params["module"])) {
		$module = $params["module"];
	} else {
		$module = $w->_module;
	}

	$widgets = $w->Widget->getWidgetsForModule($module);
	if (!empty($widgets)) {
		foreach($widgets as &$widget) {
			// Give each widget_config db object an instance of the matching class
			$widget_class = null;
			$widgetname = $widget->widget_name;
			if (class_exists($widgetname)) {
				$widget->widget_class = new $widgetname($w, $widget);
			}
		}
	}
					
	$w->ctx("columns", 3);
	$w->ctx("widgets", $widgets);
	$w->ctx("module", $module);
}