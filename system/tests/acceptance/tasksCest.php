<?php
class tasksCest
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
		$I->amGoingTo('Create a task group');
		$I->createTaskGroup($I,'testgroup',[
			'task_group_type'=>'To Do',
			'can_assign'=>'GUEST',
			'can_view'=>'GUEST',
			'can_create'=>'GUEST',
			'is_active'=>'Yes',
			'description'=>'A test group',
			'default_assignee_id'=>'testy tererer'
		]);
		$I->amGoingTo('Update a task group');
		$I->updateTaskGroup($I,'testgroup',[
			'title'=>'testgroup updated',
			'can_assign'=>'MEMBER',
			'can_view'=>'MEMBER',
			'can_create'=>'MEMBER',
			'is_active'=>'Yes',
			'description'=>'A test group updated',
			'default_assignee_id'=>'testy tererer'
		]);
		$I->amGoingTo('Add a member a task group');
		$I->addMemberToTaskGroup($I,'testgroup updated','testy tererer','GUEST');
		$I->amGoingTo('Update a member in a task group');
		$I->updateMemberInTaskGroup($I,'testgroup updated','testy tererer','ALL');
		$I->amGoingTo('Remove a member from a task group');
		$I->removeMemberFromTaskGroup($I,'testgroup updated','testy tererer');
		$I->amGoingTo('Delete a task group');
		$I->deleteTaskGroup($I,'testgroup updated');
	}

	
}
