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

		// goto example module (need to click twice)
		$I->click('Example');
		$I->click('Example');

		// create a new record
		$I->click('New Data');
		$I->wait(1);
		$I->fillForm($I,[
			'title'=>'test title',
			'rte:data'=>'Test Data',
			'check:example_checkbox'=>true,
			'select:select_field' =>'fred',
			//'radio:radio_field' =>'fred',  // NOT WORKING
			'autocomplete:autocomplete_field'=>'fred',
			'date:d_date_field'=>strtotime('09-05-2014 11:30'),
			'datetime:dt_datetime_field'=>strtotime('23-05-2014 14:45'),
			'time:t_time_field'=>strtotime('11-05-2014 14:55')]);
		$I->click('Save');
		$I->see('ExampleData updated');	

		// click back through list to edit
		$row = $I->findTableRowMatching($I,1,'test title');
        $context=".tablesorter tbody tr:nth-child(". $row .")";
		$I->click('Edit',$context);
        $I->wait(1);

        // update the record
		$I->fillField('title','Changed test data title');
		
		// ensure checkbox values save correctly
		$I->uncheckOption('#example_checkbox');
		$checkingCheckbox = False;
		$checkboxValue = $I->grabValueFrom('#example_checkbox');
		$I->click('Save');
		$I->see('ExampleData updated');	
		$row = $I->findTableRowMatching($I,1,'Changed test data title');
		$I->click('Edit','.tablesorter tbody tr:nth-child(' . $row . ')');
		if ($checkboxValue == $I->grabValueFrom('#example_checkbox')){
			$checkingCheckbox = true;
		}
		$I->assertTrue($checkingCheckbox);
		$I->click('Cancel');
		$row = $I->findTableRowMatching($I,1,'Changed test data title');
		
		// delete a record (disable the confirm function)
		$I->executeJS('window.confirm = function(){return true;}');
		$I->click('Delete','.tablesorter tbody tr:nth-child(' . $row . ')');
		$I->wait(1);
		$I->see('Object Deleted');
	}
	
}
