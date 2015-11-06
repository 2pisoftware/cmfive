<?php
class exampleCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // auth details
	var $username='admin';
	var $password='admin';

	public function testExample($I) {
		$I->login($I,$this->username,$this->password);
		$I->click('//*[@id="topnav_example"]/ul/li[3]/a'); //Example Home
		$I->click('New Data');
		$I->fillForm($I,array('title'=>'test title','data'=>'Test Data','check:example_checkbox'=>true));
//		$I->execJS('$("//*[@id="cmfive-modal"]/form/div[2]/button[1]").click()'
		$I->click('Save');
		$I->see('ExampleData updated');	
		$row = $I->findTableRowMatching($I,1,'test title');
                $context=".tablesorter tbody tr:nth-child(". $row .")";
		$I->click('Edit',$context);
                $I->wait(3);
		$I->fillField('data','Changed test data');
		$I->uncheckOption('#example_checkbox');
		$checkingCheckbox = False;
		$checkboxValue = $I->grabValueFrom('#example_checkbox');
		$I->click('Save');
		$I->see('ExampleData updated');	
		$row = $I->findTableRowMatching($I,1,'test title');
		$I->click('Edit','.tablesorter tbody tr:nth-child(' . $row . ')');
		if ($checkboxValue == $I->grabValueFrom('#example_checkbox')){
			$checkingCheckbox = true;
		}
		$I->assertTrue($checkingCheckbox);
		$I->click('Cancel');
                $row = $I->findTableRowMatching($I,1,'test title');
                $I->executeJS('window.confirm = function(){return true;}');
		$I->click('Delete','.tablesorter tbody tr:nth-child(' . $row . ')');
                //$I->wait(3);
		//$I->click('OK');
                $I->see('Object Deleted');
	}
	
}