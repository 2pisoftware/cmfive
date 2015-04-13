<?php

class favorites_widget extends ModuleWidget {

    public static $widget_count = 0;

    public function getSettingsForm($current_settings = null) {
        return array();
    }

    public function display() {
		//echo "crap";
		echo $this->w->partial("listfavorite",array('classname'=>'Favorite'), "favorites");
	}
}
