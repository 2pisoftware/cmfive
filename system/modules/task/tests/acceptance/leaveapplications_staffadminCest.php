<?php
use \WebGuy;
/**
 * @guy WebGuy\CM5WebGuySteps
 */
class leaveapplications_staffadminCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
	var $username='staffadminuser';
	var $password='pojpoj';
    var $databaseTable='staff_leave_application';
    var $navSelector='Leave Applications';
    var $recordName='Leave Application';
    var $recordLabel='Leave Application';
    var $subfolder='leaveapplications';
    var $moduleUrl='/staff-leaveapplications/';
    //employee id 1-1 admin 13-2 user 14-3 admin 15-4 anon
    var $validRecord=array('select'=>array('employee_id' => '3'),'input' => array('days' => '2', 'hours' => '3', 'd_start' => '2015-04-29 00:00:00'));
    var $updateData=array('input'=>array('hours'=>'5'));
    var $searches=array(
			array(
				'input'=>array('filter_employee'=>'','filter_date_from'=>'1-1-2013 00:00:00','filter_date_to'=>'1-1-2014 00:00:00'),
				'select'=>array('filter_status'=>''),
				'result'=>1
			),
			array(
				'input'=>array('filter_employee'=>'','filter_date_from'=>'1-1-2012 00:00:00','filter_date_to'=>'1-1-2017 00:00:00'),
				'select'=>array('filter_status'=>''),
				'result'=>3
			));
			
    public function runCrudTests(WebGuy\CM5WebGuySteps $I) {
		$I->runSQLQueries('sql'.DIRECTORY_SEPARATOR.$this->subfolder.DIRECTORY_SEPARATOR."init");
		$I->login($this->username,$this->password);
		$I->searchRecords($this);
		$I->createNewRecord($this);
		$I->editRecord($this);
		$I->deleteRecord($this);
	}
	
}
