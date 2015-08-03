<?php

class InstallService extends DbService {
	
	/**
	 * Will write data to the twig config template where variables match
	 * 
	 * @param <Array> $data
	 * @return int or FALSE
	 */
	public static function saveConfigData($data) {
		$template_path = "system/modules/install/assets/config.php";
		require_once 'Twig-1.13.2/lib/Twig/Autoloader.php';
		Twig_Autoloader::register();

		$template = null;
		if (file_exists($template_path)) {
			$dir = dirname($template_path);
			$loader = new Twig_Loader_Filesystem($dir);
			$template = str_replace($dir . DIRECTORY_SEPARATOR, "", $template_path);
		} else {
			$loader = new Twig_Loader_String();
			$template = $template_path;
		}
		
		// Render data in config
		$twig = new Twig_Environment($loader, array('debug' => true));
		$twig->addExtension(new Twig_Extension_Debug());

		$config_template = $twig->loadTemplate($template);
		$result_config = $config_template->render($data);
		
		return file_put_contents($template_path, $result_config);
	}
	
	/**
	 * Will put the final config in the project
	 * 
	 * @return null
	 */
	public static function writeConfigToProject() {
		copy("system/modules/install/assets/config.php", "config.php");
	}
	
	public static function resetConfigFile() {
		
	}
	
}