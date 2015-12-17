<?php

class WidgetService extends DbService {

	public function getWidget($destination, $source, $widget) {
		return $this->getObject("WidgetConfig", array(
			"destination_module" => $destination, 
			"source_module" => $source, 
			"widget_name" => $widget,
			"is_deleted" => 0)
		);
	}

	public function getAll() {
		return $this->getObjects("WidgetConfig", array("is_deleted" => 0));
	}

	public function getWidgetsForModule($destination_module, $user_id) {
		return $this->getObjects("WidgetConfig", array("user_id" => $user_id, "destination_module" => $destination_module, "is_deleted" => 0));
	}

	public function getWidgetNamesForModule($module) {
		return $this->w->moduleConf($module, "widgets");
	}

	public function getWidgetById($id) {
		return $this->getObject("WidgetConfig", array("id" => $id, "is_deleted" => 0));
	}

}
