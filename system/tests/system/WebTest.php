<?php

// Need to fake server vars
$_SERVER['HTTPS'] = '';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../');
require "../web.php";

class WebTest extends PHPUnit_Framework_TestCase {
	
	public $web;
	
	public function setUp() {
		parent::setUp();

		$this->web = $this->getMockBuilder("Web")->getMock();
	}
	
	/**
	 * Testing Web->enqueueScript($script)
	 */
	public function testEnqueueScript() {
		$this->web->enqueueScript("/system/test");
		
		// Test one value
		$this->assertNotEmpty($this->web->_scripts);
		$this->assertEquals($this->web->_scripts, ["/system/test"]);
		
		// Test another value
		$this->web->enqueueScript("/system/another_test");
		$this->assertEquals($this->web->_script, ["/system/test", "/system/another_test"]);
		
		// Test that adding a previous value isnt duplicated
		$this->web->enqueueScript("/system/another_test");
		$this->assertEquals($this->web->_script, ["/system/test", "/system/another_test"]);
	}
	
	/**
	 * Testing Web->enqueueStyle($style)
	 */
	public function testEnqueueStyle() {
		$this->web->enqueueStyle("/system/test");
		
		// Test one value
		$this->assertNotEmpty($this->web->_styles);
		$this->assertEquals($this->web->_styles, ["/system/test"]);
		
		// Test another value
		$this->web->enqueueStyle("/system/another_test");
		$this->assertEquals($this->web->_styles, ["/system/test", "/system/another_test"]);
		
		// Test that adding a previous value isnt duplicated
		$this->web->enqueueStyle("/system/another_test");
		$this->assertEquals($this->web->_styles, ["/system/test", "/system/another_test"]);
	}
	
	public function tearDown() {
		unset($this->web);
		parent::tearDown();
	}
}