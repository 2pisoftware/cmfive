<?php
use \AcceptanceTester;
/**
 * @guy AcceptanceTester\CM5WebGuySteps
 */
class users_adminCest
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
	// label of menu link to this module/submodule
    var $navSelector='List Users';    
    
    var $createdMessage='added';
    var $updatedMessage='updated';
    var $deletedMessage='User';
    var $subfolder='';
    var $moduleUrl='/admin/users/';
    
    var $databaseTable='user';    
    var $validRecord=array('input'=>array('login' => 'auser','password'=>'pojpoj','password2'=>'pojpoj','firstname'=>'Joe','lastname'=>'Bloggs'),'select'=>array(),'checkbox'=>array('is_admin'=>'1','is_active'=>'1'));
    var $validDBRecord=array('login' => 'auser');
  
    var $updateData=array('input'=>array('login'=>'buser'),'select'=>array());
    var $updatedDBRecord=array('login' => 'buser');
    

	
	public function runTests(AcceptanceTester\CM5WebGuySteps $I) {
		$I->runSQLQueries('users');
		$I->login($this->username,$this->password);
		$I->createNewRecord($this);
		$I->runSQLQueries('users');
		$I->doEditRecord($this->navSelector,'.editbutton[href="/admin/useredit/{id}/box"]',$this->updatedMessage,$this->databaseTable, $this->validRecord,$this->updateData, $this->validDBRecord,$this->updatedDBRecord);
		//$I->runSQLQueries('users');
		//$I->deleteRecord($this);
		// TODO PERMISSIONS EDIT
		// TODO IMPLEMENT DELETE
	}
    

	
}
