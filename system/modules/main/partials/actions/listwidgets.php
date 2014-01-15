<?php

function listwidgets_ALL(Web $w, $params) {
	$module = null;
	if (!empty($params["module"])) {
		$module = $params["module"];
	} else {
		$module = $w->_module;
	}

	$widgets = $w->Widget->getWidgetsForModule($module);
	$w->ctx("columns", 3);
	$w->ctx("widgets", $widgets);
	$w->ctx("module", $module);
}