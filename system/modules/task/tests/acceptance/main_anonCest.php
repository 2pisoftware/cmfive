<?php
use \AcceptanceTester;
/**
 * @guy AcceptanceTester\CM5WebGuySteps
 */
class main_anonCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
	var $username='user';
	var $password='pojpoj';
    var $navSelector='Home';
     
    public function runTests(AcceptanceTester\CM5WebGuySteps $I) {
		$I->login($this->username,$this->password);
		$I->see($this->navSelector);
	}
	

	
}
