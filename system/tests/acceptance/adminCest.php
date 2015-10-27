<?php
class adminCest
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



	public function login($I) {
		$I->login($I,$this->username,$this->password);
	}

}
