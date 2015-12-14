<?php
class exampleGenericCRUDCest
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
	
	// module/submodule meta data required by generic crud
	// used for database integration
	var $databaseTable='example_data';
    // top menu label
    var $navSelector='Example';
    var $recordName='Data';
    var $recordLabel='ExampleData';
    var $subfolder='';
    var $moduleUrl='/example/';
	// assigned below, array of sample data using : key seperators to specify meta data
    var $validRecord=[];
    // assigned below, array of sample data as it looks in the database
    // these fields must uniquely identify the record (as we do not have access to created record id)
    var $validDBRecord=['title'=>'test title'];
    // array of sample data to update a record using : key seperators to specify meta data
    var $updateData=array('rte:data'=>'Updated test data');
    

	public function testExample($I) {
		$I->login($I,$this->username,$this->password);
		// create
		$this->validRecord=[
			'title'=>'test title',
			'rte:data'=>'Test Data',
			'check:example_checkbox'=>true,
			'select:select_field' =>'fred',
			//'radio:radio_field' =>'fred',  // NOT WORKING
			'autocomplete:autocomplete_field'=>'fred',
			'date:d_date_field'=>strtotime('09-05-2014 11:30'),
			'datetime:dt_datetime_field'=>strtotime('23-05-2014 14:45'),
			'time:t_time_field'=>strtotime('11-05-2014 14:55')];
		//$I->createNewRecord($I,$this); // not flexible enough
		$I->doCreateNewRecord($I,$this->navSelector,'New Data','Save',$this->recordLabel.' updated',$this->databaseTable, $this->validRecord);
		// update stuffd
		// $I->editRecord($I,$this);//// not flexible enough
		$I->doEditRecord($I,$this->navSelector,$this->moduleUrl.'edit/','Save',$this->recordLabel.' updated',$this->databaseTable, $this->validDBRecord,$this->updateData);
		// delete
		$I->doDeleteRecord($I,$this->navSelector, $this->moduleUrl.'delete/','Object Deleted',  $this->databaseTable,$this->validDBRecord);
		
	}
	
}
