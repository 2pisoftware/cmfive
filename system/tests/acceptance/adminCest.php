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
	
	public function testGroupAdmin($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUserGroup($I,'testgroup');
		$I->seeLink('testgroup');
		$I->updateUserGroup($I,'testgroup','new test group');
		$I->seeLink('new test group');
		$I->deleteUserGroup($I,'new test group');
	}
	
	

}
