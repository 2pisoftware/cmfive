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
		$I->fillForm($I,array('title'=>'test title','data'=>'Test Data','check:check'=>true));
//		$I->execJS('$("//*[@id="cmfive-modal"]/form/div[2]/button[1]").click()'
		$I->click('Save');
		$I->see('ExampleData updated');	
		$row = $I->findTableRowMatching($I,1,'test title');
		$I->click('Edit','.tablesorter tbody tr:nth-child(' . $row . ')');
		$I->fillField('data','Changed test data');
		$I->uncheckOption('#check');
		$checkingCheckbox = False;
		$checkboxValue = $I->grabValueFrom('#check');
		$I->click('Save');
		$I->see('ExampleData updated');	
		$row = $I->findTableRowMatching($I,1,'test title');
		$I->click('Edit','.tablesorter tbody tr:nth-child(' . $row . ')');
		if ($checkboxValue == $I->grabValueFrom('#check')){
			$checkingCheckbox = true;
		}
		$I->assertTrue($checkingCheckbox);
		
		
	}
	
}