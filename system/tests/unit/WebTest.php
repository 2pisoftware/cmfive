<?php
/*
 * 
 * 
TODO
	* 
	
	function test_validateCSRF() {}
	
	function test_WebTemplate() {}
	function test_set($name, $value) {}
	function test_set_vars($vars, $clear = false) {}
	function test_fetch($file) {}

	function test_CachedTemplate($path, $cache_id = null, $expire = 900) {}
	function test_is_cached() {}
	function test_fetch_cache($file) {}

NOT TESTED - TRIVAL

	function test_sendHeader($key, $value) {}
	function test_cmp_weights($a, $b) {}
	function test_install() {}
			
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
	
	function test_putTemplate($key, $template) {}
	function test_templateOut($template) {}
	function test_out($txt) {}
	function test_webroot() {}
	
	function test_setLayout($l) {}
	function test_getLayout($l) {}
	function test_setTemplate($t) {}
	function test_getTemplate() {}
	function test_setTemplatePath($path) {}
	function test_setTemplateExtension($ext) {}
	function test_modules() {}
	
STUBBED
	// these methods are stubbed so cannot be tested INSIDE THIS TEST CLASS
	// TODO need to test these functions in  another test class where they are not stubbed in the Web instance
	function test_checkAccess($msg = "Access Restricted") {}
	
	function test_redirect($url) {}
	function test_sendHeader($key, $value) {}
	function test_error($msg, $url = "") {}
	function test_msg($msg, $url = "") {}
	function test_notFoundPage() {}
	
	function test_session($key, $value = null) {}
	function test_sessionUnset($key) {}
	function test_sessionDestroy() {}

NOT TESTED YET - HARD	 

	function test_start($init_database = true) {}
	
	// stubs ?? WITH STUB::once,exactly
	function test_callHook($module, $function, $data = null) {}
	function test__callPreListeners() {}
	function test__callPostListeners() {}
	
	// file download ??  handle on direct output??
	function test_sendFile($filename) {}
	
	
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


*/

namespace WebTest {
use \Codeception\Util\Stub;

// disable header function
function header($a) {
	echo("::HEADER::".$a);
}
		
	
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
			//self::$web = new Web(); //Stub::construct('Web');;
			$overrideFunctions=[];
			$overrideFunctions['dump']='::DUMP::';
			
			// REDIRECTS 
			$overrideFunctions['redirect']=function($url) {
				echo('::REDIRECT::'.$url);
			};
			$overrideFunctions['error']=function($msg,$url) {
				echo('::ERROR::'.$msg.'::'.$url); 
			};
			$overrideFunctions['msg']=function($msg,$url) {
				echo('::MESSAGE::'.$msg.'::'.$url); 
			};
			$overrideFunctions['notFoundPage']=function() {
				echo('::NOTFOUNDPAGE::'); 
			};
			
			// SESSION
			$overrideFunctions['session']=function($key, $value) {
			//	codecept_debug('::SESSION::'.$key.'::'.$value);
			};
			$overrideFunctions['sessionUnset']=function($key) {
				//codecept_debug('::SESSIONUNSET::'.$key);
			};
			$overrideFunctions['sessionDestroy']=function() {
				//codecept_debug('::SESSIONDESTROY::');
			};
			$blankFunctions=[];
			foreach ($blankFunctions as $functionName) {
				$overrideFunctions[$functionName]='';
			}
			// create stubbed Web
			self::$web = Stub::construct("Web",[],$overrideFunctions);
			// override Auth service with stub
			self::$web->_services['Auth']=Stub::make("AuthService",[
				'login'=>'',
				'allowed'=>function($path,$url) {
					if (strpos($path,'admin')===0) {
						return false;
					} else { 
						return $url;
					}
				},
				'loggedIn'=>true,
				'user'=>function() {
					return Stub::make('User',[]);
				}
			]);
		}
	}
	
	
	private function captureOutput($class,$functionToRun,$arguments=[]) {
		ob_start();
		call_user_func_array(array($class,$functionToRun),$arguments);
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
		$this->assertEquals(self::$web->request('arrayparam'),['data'=>'!~#$ &*(']);
	}
	
	function test_getCommandPath() {
		$_SERVER['REQUEST_URI']='site/tasks/showfriends/4';
		$_SERVER['SCRIPT_NAME']='site/index.php';
		// with leading path
		$this->assertEquals(self::$web->_getCommandPath(),['tasks','showfriends','4']);
		// with parameter
		$this->assertEquals(self::$web->_getCommandPath('site/admin/users/find/me/some/bread'),['admin','users','find','me','some','bread']);
		// without leading path
		$this->assertEquals(self::$web->_getCommandPath('admin/users/find/me/some/bread'),['admin','users','find','me','some','bread']);
		//* eg /site/users/do/2 + site/index.php  => [users,do,2]
	}
	
	function test_pathMatch() {
		$_SERVER['REQUEST_URI']='site/tasks/showfriends/4';
		$_SERVER['SCRIPT_NAME']='site/index.php';
		self::$web->_paths=self::$web->_getCommandPath();
		$this->assertEquals(self::$web->pathMatch('module','action','id'),['module'=>'tasks','action'=>'showfriends','id'=>'4']);
	}
	
	
	function test_getModuleDir() {
		// system
		$this->assertEquals(self::$web->getModuleDir('task'),'system/modules/task/');
		// non system
		$this->assertEquals(self::$web->getModuleDir('example'),'modules/example/');
	}
	
	
	function stripDomainFromUrl($taskUrl) {
		$after=explode('/',$taskUrl);
		if (strpos($taskUrl,'http://')===0) { 
			$taskUrl=substr($taskUrl,7);
			$after=array_slice(explode('/',$taskUrl),1);
		} else if (strpos($taskUrl,'https://')===0) {
			$taskUrl=substr($taskUrl,8);
			$after=array_slice(explode('/',$taskUrl),1);
		}
		
		return implode("/",$after);
	}
	
	function test_moduleUrl() {
		$this->assertEquals($this->stripDomainFromUrl(self::$web->moduleUrl('task')),'system/modules/task/');
		$this->assertEquals($this->stripDomainFromUrl(self::$web->moduleUrl('example')),'modules/example/');
		
	}
	
	function test_service() {
		//function test___get($name) {}
		$s=self::$web->service('task');
		if (!$s instanceof TaskService) {
			$this->fail('No task service created');
		}
		$s=self::$web->service('example');
		if (!$s instanceof ExampleService) {
			$this->fail('No example service created');
		}
	}
	
	function test_getModuleNameForModel() {
		$this->assertEquals(self::$web->getModuleNameForModel('TaskGroup'),'task');
		$this->assertEquals(self::$web->getModuleNameForModel('ExampleData'),'example');
	}
	
	function test_isClassActive() {
		$this->assertTrue(self::$web->isClassActive('TaskGroup'));
		$this->assertTrue(self::$web->isClassActive('ExampleData'));
		$this->assertFalse(self::$web->isClassActive('TaskGroupDDDD'));
	}
	
	function test_errorMessage() {
		$user=Stub::make('User',[]);
		// empty cases
		$output=$this->captureOutput(self::$web,'errorMessage',[$user, '', false, false,  "/"]);
		$this->assertNull($output);
		$output=$this->captureOutput(self::$web,'errorMessage',[$user, 'user', true, false,  "/"]);
		$this->assertNull($output);
		
		// simple case
		$output=$this->captureOutput(self::$web,'errorMessage',[$user, 'user', false, false,  "/"]);
		$this->assertEquals($output,'::ERROR::Creating this user failed.::/');
		// change type and isUpdate
		$output=$this->captureOutput(self::$web,'errorMessage',[$user, 'frog', false, true,  "/"]);
		$this->assertEquals($output,'::ERROR::Updating this frog failed.::/');
		// add validation messages
		$output=$this->captureOutput(self::$web,'errorMessage',[$user, 'frog', ['invalid'=>['username'=>['cannot be empty'],'age'=>['must be older']]], true,  "/"]);
		$this->assertEquals($output,
			"::ERROR::Updating this frog failed because<br/><br/>\nUsername: cannot be empty <br/>\nAge: must be older <br/>\n::/"
		);
		
	}
	
	function test_getMimetype() {
		// from sample files in _data folder
		$this->assertEquals(self::$web->getMimetype('..'.DIRECTORY_SEPARATOR.'_data'.DIRECTORY_SEPARATOR.'test.txt'),'text/plain');
		$this->assertEquals(self::$web->getMimetype('..'.DIRECTORY_SEPARATOR.'_data'.DIRECTORY_SEPARATOR.'cat.jpg'),'image/jpeg');
	}


	function test_initDB() {
		self::$web->initDB();
		if (! self::$web->db instanceof DbPDO) {
			$this->fail('No database connection found');
		}
	}
	
	function test_localUrl() {
		$webRoot=self::$web->webroot();
		// with and without leading slash
		$this->assertEquals(self::$web->localUrl('tasks/edit/1'),$webRoot.'/tasks/edit/1');
		$this->assertEquals(self::$web->localUrl('/tasks/edit/1'),$webRoot.'/tasks/edit/1');
	}   
	
	function test_internalLink() {
		// generate link
		$this->assertEquals(self::$web->internalLink('Title','task','delete'),"<a href='http://localhost/task/delete'>Title</a>");
		// no access
		$this->assertNull(self::$web->internalLink('Title','admin','edit'));
	}
	
	
	function test_menuLink() {
		$links=[];
		// not allowed empty
		$this->assertFalse(self::$web->menuLink('admin/users/2','Admin User 2',$links));
		$expect="<a href='http://localhost/task/edit/2' >Edit Task 2</a>";
		$this->assertEquals(self::$web->menuLink('task/edit/2','Edit Task 2',$links),$expect);
		// check links array
		$this->assertEquals($links,[0=>'',1=>$expect]);
	}

	function test_menuButton() {
		$links=[];
		// not allowed empty
		$this->assertFalse(self::$web->menuButton('admin/users/2','Admin User 2',$links));
		$expect="<button class=\"button tiny \" onclick=\"parent.location='http://localhost/task/edit/2'; return false;\" >Edit Task 2</button>";
		//codecept_debug(self::$web->menuButton('task/edit/2','Edit Task 2'));
		$this->assertEquals(self::$web->menuButton('task/edit/2','Edit Task 2',$links),$expect);
		// check links array
		$this->assertEquals($links,[0=>'',1=>$expect]);
	} 
	
	function test_menuBox() {
		$links=[];
		// not allowed empty
		$this->assertFalse(self::$web->menuBox('admin/users/2','Admin User 2',$links));
		$expect="<a onclick=\"modal_history.push(&quot;http://localhost/task/edit/2?isbox=1&quot;); $(&quot;#cmfive-modal&quot;).foundation(&quot;reveal&quot;, &quot;open&quot;, &quot;http://localhost/task/edit/2?isbox=1&quot;);return false;\">Edit Task 2</a>";
		$this->assertEquals(self::$web->menuBox('task/edit/2','Edit Task 2',$links),$expect);
		// check links array
		$this->assertEquals($links,[0=>'',1=>$expect]);
	} 

	// WORKING 	
	function test_loadConfigurationFiles() {
		//function test_scanModuleDirForConfigurationFiles($dir = "") {}
		// clear existing configuration
		$cachefile = ROOT_PATH . "/cache/config.cache";
		unlink($cachefile);
		foreach (Config::keys(true) as $key) {
			Config::set($key,NULL);
		}
		// now reload
		self::$web->loadConfigurationFiles();
		// and check
		if (!file_exists($cachefile)) {
			$this->fail('Cache file was not written');
		}
		// system config setting
		$this->assertEqual(Config::get('testing.system'),'fred');
		// system module config setting
		$this->assertEqual(Config::get('testing.systemmodule'),'fred');
		// module config setting
		$this->assertEqual(Config::get('testing.system'),'fred');
		// site config setting
		$dbUser=Config::get('database.username');
		$this->assertTrue(strlen(Config::get('database.username'))>0);
		// check cache loading
		// change value then reload and check value reverted
		Config::set('database.username','fred');
		self::$web->loadConfigurationFiles();
		$this->assertEquals(Config::get('database.username'),$dbUser);
	}
	
	function test_partial() {} //$name, $params = null, $module = null, $method = "ALL"
	function test_templateExists() {}  //$name
	function test_getTemplateRealFilename() {}  //$tmpl
	function test_fetchTemplate() {}  //$name = null

		
}  // class
}  // namespace
