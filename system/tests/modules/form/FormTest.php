<?php

$_SERVER['HTTPS'] = '';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../../');

$_SESSION['user_id'] = 1;

require $_SERVER['DOCUMENT_ROOT'] . "/system/web.php";
require $_SERVER['DOCUMENT_ROOT'] . "/system/modules/auth/models/User.php";
require $_SERVER['DOCUMENT_ROOT'] . "/system/modules/form/models/Form.php";
require $_SERVER['DOCUMENT_ROOT'] . "/system/modules/form/models/FormField.php";

class FormTest extends PHPUnit_Framework_TestCase {
	private $form;
	private $form_field;
	
	public function setUp() {
		$this->form = $this->getMockBuilder("Form")->getMock();
		$this->form_field = $this->getMockBuilder("FormField")->getMock();
	}
	
	
	public function testGetFields() {
//		$this->assertEmpty($this->form->getFields());
	}
	
	public function testGetSelectOptionTitle() {
		$title = "test";
		$this->form->title = $title;
		
		$this->assertEquals($title, $this->form->getSelectOptionTitle());
	}
	
	public function testGetSelectOptionValue() {
		$id = 1;
		$this->form->id = $id;
		
		$this->assertEquals($id, $this->form->getSelectOptionValue());
	}
	
	public function testPrintSearchTitle() {
		$title = "test";
		$this->form->title = $title;
		
		$this->assertEquals($title, $this->form->printSearchTitle());
	}
	
	public function testPrintSearchUrl() {
		$url = '/form/show/1';
		$this->form->id = 1;
		
		$this->assertEquals($url, $this->form->printSearchUrl());
	}
	
	public function tearDown() {
		
	}
	
}