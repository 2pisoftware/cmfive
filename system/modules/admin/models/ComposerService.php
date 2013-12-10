<?php

class ComposerService extends DbService {
	private static $MAX_COMPOSER_FILES = 2;

	public function getAll() {
		return $this->getObjects("ComposerChecksums");
	}

	public function count() {
		return $this->_db->get("composer_checksums")->count();
	}

	// Determines whether new composer.json files should be added
	// Stops adding files and updating running more than once
	public function shouldAddFiles() {
		return (!$this->count() == ComposerService::$MAX_COMPOSER_FILES);
	}

	// Adds composer.json files to DB
	public function addComposerFiles() {
		// Destroy everything in composer_checksums
		$c_checksums = $this->getAll();
		if (!empty($c_checksums)) {
			foreach ($c_checksums as $chk) {
				$chk->delete();
			}
		}
		
		// Add new entries and update
		// Use empty checksums so updates are run for the first time
		$chk = new ComposerChecksums($this->w);
		$chk->location = SYSTEM_PATH . "/composer.json";
		$chk->insertOrUpdate();

		$chk_user = new ComposerChecksums($this->w);
		if (!file_exists(ROOT_PATH . "/composer.json")){
			// Hardcode config settings into user composer.json
			file_put_contents(ROOT_PATH . "/composer.json", 
<<<COMPOSER
{
	"config": {
		"vendor-dir": "system/composer/vendor",
		"cache-dir": "system/composer/cache",
		"bin-dir": "system/composer/bin"
	},
	"require": {

	}
}
COMPOSER
);
		}
		$chk_user->location = ROOT_PATH . "/composer.json";
		$chk_user->insertOrUpdate();

		$this->w->redirect("/admin/composer");
	}

	// Helper functions http://stackoverflow.com/questions/1281140/run-process-with-realtime-output-in-php
	// public function disable_ob() {
	//     // Turn off output buffering
	//     ini_set('output_buffering', 'off');
	//     // Turn off PHP output compression
	//     ini_set('zlib.output_compression', false);
	//     // Implicitly flush the buffer(s)
	//     ini_set('implicit_flush', true);
	//     ob_implicit_flush(true);
	//     // Clear, and turn off output buffering
	//     while (ob_get_level() > 0) {
	//         // Get the curent level
	//         $level = ob_get_level();
	//         // End the buffering
	//         ob_end_clean();
	//         // If the current level has not changed, abort
	//         if (ob_get_level() == $level) break;
	//     }
	//     // Disable apache output buffering/compression
	//     if (function_exists('apache_setenv')) {
	//         apache_setenv('no-gzip', '1');
	//         apache_setenv('dont-vary', '1');
	//     }
	// }

}