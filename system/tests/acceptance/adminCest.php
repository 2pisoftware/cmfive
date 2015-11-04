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
		$I->createUserGroup($I,'new test group');
		$I->seeLink('new test group');
		$I->updateUserGroup($I,'new test group','testgroup');
		$I->seeLink('testgroup');
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->addUserToUserGroup($I,'testuser','testy tererer','testgroup');
		$I->removeUserFromUserGroup($I,'testuser','testy tererer','testgroup');
		$I->setUserGroupPermissions($I,'testgroup',array('comment','user','favorites_user'));
		$I->deleteUserGroup($I,'testgroup');
		$I->deleteUser($I,'testuser');
	}
	
	
	public function testCreateTask($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->createTaskGroup($I,'testgroup',[
			'task_group_type'=>'To Do',
			'can_assign'=>'GUEST',
			'can_view'=>'GUEST',
			'can_create'=>'GUEST',
			'is_active'=>'Yes',
			'description'=>'A test group',
			'default_assignee_id'=>'testy tererer'
		]);
		$I->createTask($I,'testgroup','test task',[
			'task_group_id'=>'testgroup',
			'task_type'=>'To Do',
			'title'=>'test task',
			'status'=>'New',
			'priority'=>'Normal',
			//'date:dt_due'=>$data['dt_due'],
			'assignee_id'=>'testy tererer',
			'estimate_hours'=>10,
			'effort'=>11,
			'description'=>'a test task',
		]);
	}
	
	public function testTasks($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->lookForwardTo('Create a task group');
		$I->createTaskGroup($I,'testgroup',[
			'task_group_type'=>'To Do',
			'can_assign'=>'GUEST',
			'can_view'=>'GUEST',
			'can_create'=>'GUEST',
			'is_active'=>'Yes',
			'description'=>'A test group',
			'default_assignee_id'=>'testy tererer'
		]);
		$I->wantTo('Update a task group');
		$I->updateTaskGroup($I,'testgroup',[
			'title'=>'testgroup updated',
			'can_assign'=>'MEMBER',
			'can_view'=>'MEMBER',
			'can_create'=>'MEMBER',
			'is_active'=>'Yes',
			'description'=>'A test group updated',
			'default_assignee_id'=>'testy tererer'
		]);
		$I->wantTo('Add a member a task group');
		$I->addMemberToTaskGroup($I,'testgroup updated','testy tererer','GUEST');
		$I->wantTo('Update a member in a task group');
		$I->updateMemberInTaskGroup($I,'testgroup updated','testy tererer','ALL');
		$I->wantTo('Remove a member from a task group');
		$I->removeMemberFromTaskGroup($I,'testgroup updated','testy tererer');
		$I->comment('Delete a task group');
		$I->deleteTaskGroup($I,'testgroup updated');
	}

}
