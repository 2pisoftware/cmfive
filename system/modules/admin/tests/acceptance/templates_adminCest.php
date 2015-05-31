<?php
use \AcceptanceTester;
/**
 * @guy AcceptanceTester\CM5WebGuySteps
 */
class templates_adminCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
	var $username='admin';
	var $password='admin';
    var $navSelector='List Users';
    
    var $databaseTable='user';
    var $recordName='User';
    var $recordLabel='User';
    var $subfolder='';
    var $moduleUrl='/admin/users/';
    var $validRecord=array('input'=>array('login' => 'auser','password'=>'ca1e51f19afbe6e0fb51dde5bcf01ab73e52c7cd','password_salt'=>'9b618fbc7f9509fc28ebea98becfdd58'),'select'=>array('contact_id'=>'1'),'checkbox'=>array('is_admin'=>'1'));
    var $updateData=array('input'=>array('login'=>'buser'),'select'=>array());
    /*var $searches=array(
			array(
				'input'=>array('filter_employee'=>'1','filter_date_from'=>'1-1-2013 00:00:00','filter_date_to'=>'1-1-2014 00:00:00'),
				'select'=>array('filter_status'=>''),
				'result'=>1
			),
			array(
				'input'=>array('filter_employee'=>'1','filter_date_from'=>'1-1-2012 00:00:00','filter_date_to'=>'1-1-2017 00:00:00'),
				'select'=>array('filter_status'=>''),
				'result'=>3
			));
	*/		
	public function runTests(AcceptanceTester\CM5WebGuySteps $I) {
		//$I->runSQLQueries('all');
		//$I->login($this->username,$this->password);
		//$I->searchRecords($this);
		//$I->createNewRecord($this);
		//$I->editRecord($this);
		//$I->deleteRecord($this);
		
	}
    

	
}
