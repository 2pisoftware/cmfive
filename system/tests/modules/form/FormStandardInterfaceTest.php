<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../../');

require $_SERVER['DOCUMENT_ROOT'] . '/system/modules/form/models/FormFieldInterface.php';
require $_SERVER['DOCUMENT_ROOT'] . '/system/modules/form/models/FormStandardInterface.php';

class FormStandardInterfaceTest extends PHPUnit_Framework_TestCase {
	
	private $interface;
	
	public function setUp() {
		$this->interface = "FormStandardInterface";
	}
	
	public function testFormType() {
		$interface = $this->interface;
		
		// Test random value
		$this->assertEmpty($interface::formType("thisisavaluethattheinterfacedoesntknowabout"));
		
		// Test for default "text"
		$this->assertEquals("text", $interface::formType("text"));
		
		// Test date
		$this->assertEquals("date", $interface::formType("date"));
		
		// Test datetime
		$this->assertEquals("datetime", $interface::formType("datetime"));
	}
	
	public function testMetadataForm() {
		$interface = $this->interface;
		
		// Check values that are unknown
		$this->assertEmpty($interface::metadataForm("thisisavaluethattheinterfacedoesntknowabout"));
		
		// Test for decimal (case insensitive)
		$form = $interface::metadataForm("DECIMAL");
		
		// Assert that an array of three values is returned
		$this->assertInstanceOf("array", $form);
		$this->assertCount(3, array_values($form));
		
		// Check values that dont have a form (but can be responded to)
		$this->assertEmpty($interface::metadataForm("text"));
	}
	
	public function testModifyForDisplay() {
		$interface = $this->interface;
		
		// Check values that are unknown
		$this->assertEquals("abc", $interface::modifyForDisplay("thisisavaluethattheinterfacedoesntknowabout", "abc"));
		
		// Test decimal
		$this->assertEquals(1.2345, $interface::modifyForDisplay("decimal", "1.2345"));
		
		// Test its use of metadata (by faking it)
		$metadata = new stdClass();
		$metadata->meta_key = "decimal_places";
		$metadata->meta_value = 3;
		$this->assertEquals(1.235, $interface::modifyForDisplay("decimal", "1.2345", [$metadata]), 0.1);
		
		// Test date
		$this->assertEquals("01/10/2015", $interface::modifyForDisplay("date", "1443704400"));
		
		// Test date time (Australia/Sydney timezone)
		$this->assertEquals("01/10/2015 13:00:00", $interface::modifyForDisplay("date", "1443704400"));
		
		// Test default
		$this->assertEquals("1.2345", $interface::modifyForDisplay("text", "1.2345"));
	}

	public function testModifyForPersistance() {
		$interface = $this->interface;
		
		// Check values that are unknown
		$this->assertEquals("abc", $interface::modifyForPersistance("thisisavaluethattheinterfacedoesntknowabout", "abc"));
		
		// Check date/datetime
		$this->assertEquals(1443704400, $interface::modifyForPersistance("datetime", "01/10/2015 23:00:00"));
		
		// Check default
		$this->assertEquals("1.2345", $interface::modifyForPersistance("text", "1.2345"));
	}
}