<?php
/**
 * widget for displaying favoritres 
 *
 * @author Steve Ryan, steve@2pisystems.com, 2015
 **/

class favorites_widget extends ModuleWidget {

    public static $widget_count = 0;

    public function getSettingsForm($current_settings = null) {
        return array();
    }

    public function display() {
		echo $this->w->partial("listfavorite",array('classname'=>'Favorite'), "favorites");
	}
}
