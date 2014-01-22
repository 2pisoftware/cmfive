<?php

class TestProcessor extends ProcessorType {

	public function getSettingsForm($current_settings = null) {
		// Check if json
		if (!empty($current_settings)) {
			if (is_string($current_settings)) {
				$current_settings = json_decode($current_settings);
			}
		}

		return array("Settings" => array(
			array(
				array("My Setting", "text", "my_setting", @$current_settings->my_setting)
			)
		));
	}

	public function doJob() {
		echo "I am doing something";
	}

}