<?php
use \AcceptanceTester;
/**
 * @guy AcceptanceTester\CM5WebGuySteps
 */
class main_adminCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
	var $username='admin';
	var $password='admin';
    var $navSelector='Home';
    
    var $tableName='task';
    var $className='Task';
    var $sampleObject=array('id'=>'87677787','title'=>'do stuff');
    
    private function getJSON($url,$I) {
		$I->amOnPage($url);
		$json=$I->getVisibleText();
		$objects=json_decode($json);
		return $objects;
	}
     
    public function runTests(AcceptanceTester\CM5WebGuySteps $I) {
		// authenticate and get REST token
		$I->login($this->username,$this->password);
		$token='token='.$this->getJSON("/rest/token?api=abc",$I)->success;
		// load single record
		$user=$this->getJSON('/rest/index/user/id/1?'.$token,$I)->success[0];
		//if ($user->login!="admin") {
		//	$I->see('Failed to load admin user');
		//}
		// search
		
		// save record
		$I->amOnPage("/");
		$I->executeJS("
		$.post('/rest/save/".$this->className."?".$token."',".json_encode($this->sampleObject).");
		");
		$I->wait(3);
		$I->seeInDatabase($this->tableName,$this->sampleObject);
		// delete record
		$I->haveInDatabase($this->tableName,$this->sampleObject);
		$I->executeJS("
		$.post('/rest/delete/".$this->className."/".$this->sampleObject['id']."?".$token."'"");
		");
		$I->cantSeeInDatabase($this->tableName,array('id'=>$this->sampleObject['id']));   //,'deleted'=>'0'
		//$('#topnav_example a').get(0).click();");
		
	}
	

	
}
