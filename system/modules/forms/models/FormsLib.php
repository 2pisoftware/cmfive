<?php
class FormsLib {
	static function getApplicationIcon($app) {
		$path = FILE_ROOT."forms/icons/".$app->slug.".png";
		if (file_exists($path)) {
			return str_replace(ROOT,"",$path);
		}
		return $app->w->moduleUrl("forms")."/assets/images/folder_data.png";
	}
}