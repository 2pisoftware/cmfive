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

	public function testUserAdmin($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->setUserPermissions($I,'testuser',array('comment','user','favorites_user'));
		$I->updateUser($I,'testuser',array('firstname'=>'Fred' ,'lastname'=>'Flintstone','check:is_admin'=>true));
		$I->deleteUser($I,'testuser');
	}

}
