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
		codecept_debug(self::$web);
	
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
	
	
	function test_modelLoader() {
		// THIS TEST IS USELESS, IT PASSES BUT WTF. TRICKY TO GET AT PRIVATE 
		// FUNCTION MODEL LOADER EXCEPT THROUGH AUTOLOAD MECHANISM
		//codecept_debug('ML');
		// modelLoader is called by autoloader
		// in tasks module
		//$t=new Task(self::$web);
		//$t=new User(self::$web);
		$t=new ExampleData(self::$web);
		//$this->assertTrue(class_exists('Task',true));
		// is in cache?
		//codecept_debug('ML CACHE');
		//codecept_debug(self::$web->_classdirectory);  // empty ????
		// non existant class
		$this->assertFalse(class_exists('ExampleDataISNotReallyATypeOfObject',true));
	}

	function test_getSubmodules() {
		//codecept_debug(self::$web->getSubmodules('report'));
		//codecept_debug(self::$web->getSubmodules('task'));
		$this->assertTrue(self::$web->getSubmodules('report')==['connections','templates']);
	}
	
	function test_checkUrl() {
		//function test_parseUrl($url) {}
	
		$this->assertTrue(self::$web->checkUrl('tasks-groups/delete/5','tasks','groups','delete'));
		$this->assertTrue(self::$web->checkUrl('tasks-groups/delete/5','*','groups','delete'));
		$this->assertTrue(self::$web->checkUrl('tasks-groups/delete/5','tasks','*','delete'));
		$this->assertTrue(self::$web->checkUrl('tasks-groups/delete/5','tasks','groups','*'));
		$this->assertFalse(self::$web->checkUrl('tasks-groups/delete/5','tasks','groups','add'));
		$this->assertFalse(self::$web->checkUrl('tasks-groups/delete/5','tasks','friends','delete'));
		$this->assertFalse(self::$web->checkUrl('tasks-groups/delete/5','users','groups','add'));
	}
	
   
	function test_ctx() {
		// as setter
		self::$web->ctx('name','joe');
		// append
		self::$web->ctx('name',' janes',true);
		// as getter
		$this->assertTrue(self::$web->ctx('name')==="joe janes");
	}
	
	function test_validate() {
		$_REQUEST['myparam']='this is not a phone';
		$this->assertEquals(self::$web->validate([['myparam','phone:','not a phone']]),['not a phone']);
		$_REQUEST['myparam']='phone:876876876767';
		$this->assertEquals(self::$web->validate([['myparam','phone:','not a phone']]),[]);
		$this->assertNull(self::$web->validate(false));
	}
	
	function test_request() {
		// normal case
		$_REQUEST['myparam']='%21%7E%23%24+%26%2A%28';
		$_REQUEST['mynullparam']=null;
		$this->assertEquals(self::$web->request('myparam'),'!~#$ &*(');
		// non existent case
		$this->assertNull(self::$web->request('mynonexistingparam'));
		// empty value
		$this->assertEquals(self::$web->request('mynullparam'),'');
		// array
		$_REQUEST['arrayparam']=['data'=>'%21%7E%23%24+%26%2A%28'];
		codecept_debug('RAW');
		codecept_debug($_REQUEST['arrayparam']);
		codecept_debug('request');
		codecept_debug(self::$web->request('arrayparam'));
		$this->assertEquals(self::$web->request('arrayparam'),['data'=>'!~#$ &*(']);
	}
	


/*
 * 
 * 
NOT TESTED - TRIVAL
	function test_moduleConf($module, $key) {}
	function test_isAjax() {}
	function test_dump() {}
	function test_setTitle($title) {}
	function test_currentRequestMethod() {}
	function test_getPath() {}
	function test_requestIpAddress() {}
	function test_currentModule() {}
	function test_currentSubModule() {}
	function test_currentAction() {}
	
	


NOT TESTED - HARD
	// no access to header in CLI PHP
	function test_redirect($url) {}
	function test_sendHeader($key, $value) {}
	// no access to session in CLI PHP
	function test_session($key, $value = null) {}
	function test_sessionUnset($key) {}
	function test_sessionDestroy() {}
	// stubs ??
	function test_callHook($module, $function, $data = null) {}
	function test__callPreListeners() {}
	function test__callPostListeners() {}
	




function test_cmp_weights($a, $b) {}
		

	function test_loadConfigurationFiles() {}
	
	function test___construct() {}
	function test_install() {}
	function test_start($init_database = true) {}
	function test__callWebHooks($type) {}
	function test___get($name) {}
	function test_initDB() {}
	function test_scanModuleDirForConfigurationFiles($dir = "") {}
	function test_validateCSRF() {}
	

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
	
	function test_WebTemplate() {}
	function test_set($name, $value) {}
	function test_set_vars($vars, $clear = false) {}
	function test_fetch($file) {}

	function test_CachedTemplate($path, $cache_id = null, $expire = 900) {}
	function test_is_cached() {}
	function test_fetch_cache($file) {}
*/
}
