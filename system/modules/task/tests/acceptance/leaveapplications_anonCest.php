<?php
use \WebGuy;
/**
 * @guy WebGuy\CM5WebGuySteps
 */
class leaveapplications_anonCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
	var $username='anon';
	var $password='pojpoj';
    var $navSelector='Leave Applications';
    
    public function runTests(WebGuy\CM5WebGuySteps $I) {
		$I->login($this->username,$this->password);
		$I->dontSeeLink($this->navSelector);
	}
	
}
