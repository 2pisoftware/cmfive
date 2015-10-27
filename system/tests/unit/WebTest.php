<?php
use \Codeception\Util\Stub;
	
class WebTest extends  \Codeception\TestCase\Test {
	
	
	/**
	* @var \UnitGuy
	*/
	protected $guy;
	/**
     * @var \Web
     */
	protected static $web;

	
	public function _before() {
		// before is called per test so check existence before creating
		if (empty(self::$web)) {
			echo "new web";
			self::$web = new Web(); //Stub::construct('Web');;
		}
	}
	
	
	private function captureOutput($class,$functionToRun) {
		ob_start();
		call_user_func(array($class,$functionToRun));
		$generated=ob_get_contents();
		ob_end_clean();
		return $generated;
	}
	
	/*****************************************
	 * TESTS
	 *****************************************/
	
	/**
	 * Testing Web->enqueueScript($script)
	 */
	public function testEnqueueAndOutputScript() {
		self::$web->enqueueScript(array("name" => "modernizr.js", "uri" => "/system/templates/js/modernizr.js", "weight" => 10));
		
		// Test one script
		$this->assertEquals(count(self::$web->_scripts),1);
		$this->assertEquals($this->captureOutput(self::$web,'outputScripts'),"<script src='/system/templates/js/modernizr.js'></script>");
		
		// Test a second script
		self::$web->enqueueScript(array("name" => "jquery.js", "uri" => "/system/templates/js/jquery.js", "weight" => 50));
        $this->assertEquals(count(self::$web->_scripts),2);
		$this->assertEquals($this->captureOutput(self::$web,'outputScripts'),"<script src='/system/templates/js/jquery.js'></script><script src='/system/templates/js/modernizr.js'></script>");
		
		// Test that adding a previous value isnt duplicated
		self::$web->enqueueScript(array("name" => "jquery.js", "uri" => "/system/templates/js/jquery.js", "weight" => 50));
        $this->assertEquals(count(self::$web->_scripts),2);
		$this->assertEquals($this->captureOutput(self::$web,'outputScripts'),"<script src='/system/templates/js/jquery.js'></script><script src='/system/templates/js/modernizr.js'></script>");

		// Test weight based sorting by injecting another script which should sort to the middle
		self::$web->enqueueScript(array("name" => "myscript.js", "uri" => "/eek/myscript.js", "weight" => 20));
        $this->assertEquals(count(self::$web->_scripts),3);
		$this->assertEquals($this->captureOutput(self::$web,'outputScripts'),"<script src='/system/templates/js/jquery.js'></script><script src='/eek/myscript.js'></script><script src='/system/templates/js/modernizr.js'></script>");
		
	}
	
	/**
	 * Testing Web->enqueueStyle($style)
	 */
	public function testEnqueueAndOutputStyle() {
		self::$web->enqueueStyle(array("name" => "style.css", "uri" => "/system/style.css", "weight" => 10));
		
		// Test one script
		$this->assertEquals(count(self::$web->_styles),1);
		$this->assertEquals($this->captureOutput(self::$web,'outputStyles'),"<link rel='stylesheet' href='/system/style.css'/>");
		
		// Test a second script
		self::$web->enqueueStyle(array("name" => "jquery.css", "uri" => "/system/jquery.css", "weight" => 50));
        $this->assertEquals(count(self::$web->_styles),2);
		$this->assertEquals($this->captureOutput(self::$web,'outputStyles'),"<link rel='stylesheet' href='/system/jquery.css'/><link rel='stylesheet' href='/system/style.css'/>");
		
		// Test that adding a previous value isnt duplicated
		self::$web->enqueueStyle(array("name" => "jquery.css", "uri" => "/system/jquery.css", "weight" => 50));
        $this->assertEquals(count(self::$web->_styles),2);
		$this->assertEquals($this->captureOutput(self::$web,'outputStyles'),"<link rel='stylesheet' href='/system/jquery.css'/><link rel='stylesheet' href='/system/style.css'/>");

		// Test weight based sorting by injecting another style which should sort to the middle
		self::$web->enqueueStyle(array("name" => "mine.css", "uri" => "/eek/mine.css", "weight" => 20));
        $this->assertEquals(count(self::$web->_styles),3);
		$this->assertEquals($this->captureOutput(self::$web,'outputStyles'),"<link rel='stylesheet' href='/system/jquery.css'/><link rel='stylesheet' href='/eek/mine.css'/><link rel='stylesheet' href='/system/style.css'/>");
	}
	
	
	function test_loadConfigurationFiles() {}
	
	



/*
	function test___construct() {}
	function test_modelLoader($className) {}
	function test__getCommandPath($url = null) {    	}
	function test_cmp_weights($a, $b) {}
	function test_install() {}
	function test_start($init_database = true) {}
	function test__callWebHooks($type) {}
	function test___get($name) {}
	function test_initDB() {}
	function test_moduleConf($module, $key) {}
	function test_scanModuleDirForConfigurationFiles($dir = "") {}
	function test_validateCSRF() {}
	function test_getSubmodules($module) {}
	function test_isAjax() {}
	function test_checkAccess($msg = "Access Restricted") {}
	function test_getMimetype($filename) {}
	function test_sendFile($filename) {}
	function test_menuLink($path, $title, &$array = null, $confirm = null, $target = null) {}
	function test_menuButton($path, $title, &$array = null) {}
	function test_menuBox($path, $title, &$array = null) {}
	function test_localUrl($link = null) {}
	function test_error($msg, $url = "") {}
	function test_errorMessage($object, $type = null, $response = true, $isUpdating = false, $returnUrl = "/") {}
	function test_msg($msg, $url = "") {}
	function test_notFoundPage() {}
	function test_internalLink($title, $module, $action = null, $params = null) {}
	function test_modules() {}
	function test_getModuleDir($module=null) {}
	function test_moduleUrl($module) {}
	function test_service($name) {}
	function test_getModuleNameForModel($classname) {}
	function test_isClassActive($classname) {}
	function test_partial($name, $params = null, $module = null, $method = "ALL") {}
	function test_callHook($module, $function, $data = null) {}
	function test_setLayout($l) {}
	function test_getLayout($l) {}
	function test_setTemplate($t) {}
	function test_getTemplate() {}
	function test_setTemplatePath($path) {}
	function test_setTemplateExtension($ext) {}
	function test_templateExists($name) {}
	function test_getTemplateRealFilename($tmpl) {}
	function test_fetchTemplate($name = null) {}
	function test_putTemplate($key, $template) {}
	function test_templateOut($template) {}
	function test_out($txt) {}
	function test_webroot() {}
	function test_pathMatch() {}
	function test_request($key, $default = null) {}
	function test_requestIpAddress() {}
	function test_currentModule() {}
	function test_currentSubModule() {}
	function test_currentAction() {}
	function test__callPreListeners() {}
	function test__callPostListeners() {}
	function test_validate($valarray) {}
	function test_currentRequestMethod() {}
	function test_getPath() {}
	function test_ctx($key, $value = null, $append = false) {}
	function test_session($key, $value = null) {}
	function test_sessionUnset($key) {}
	function test_sessionDestroy() {}
	function test_redirect($url) {}
	function test_sendHeader($key, $value) {}
	function test_dump() {}
	function test_setTitle($title) {}
	function test_parseUrl($url) {}
	function test_checkUrl($url,$module,$submodule,$action) {}
	function test_WebTemplate() {}
	function test_set($name, $value) {}
	function test_set_vars($vars, $clear = false) {}
	function test_fetch($file) {}
	function test_CachedTemplate($path, $cache_id = null, $expire = 900) {}
	function test_is_cached() {}
	function test_fetch_cache($file) {}
*/
}
