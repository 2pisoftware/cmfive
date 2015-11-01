<?php 
namespace Helper;

/******************************************
 * Shared helper functions for working with cmFive
 * all public methods declared in helper class will be available in $I
 ******************************************/
class CmFiveTestHelper extends \Codeception\Module
{
	
	/****************************
	 * MISC FUNCTIONS
	 ****************************/

	
	public function findTableRowMatching($I,$columnNumber,$matchValue) {
		$rows=$I->grabMultiple('.tablesorter tbody tr td:nth-child('.$columnNumber.')');
		if (is_array($rows))  {
			foreach ($rows as  $k=>$v) {
				$index=$k + 1;
				if (trim($v)==trim($matchValue)) {
					return $index;
				}
			}
		}
		return 0;
	}

	/**
	 * Fill a form from an array of data
	 * Assume that id attribute of form inputs match $data keys
	 * where key starts with check: or select: a modified key is used
	 * with the setOption or checkOption commands to set values
	 * otherwise the input is treated as text using fillField 
	 */	 
	public function fillForm($I,$data) {
		if (is_array($data)) {
			foreach ($data as $fieldName=>$fieldValue) {
				$fieldNameParts=explode(':',$fieldName);
				if ($fieldNameParts[0]=='check' && count($fieldNameParts)>1) {
					if ($fieldValue) {
						 $I->checkOption('#'.$fieldNameParts[1]);
					} else {
						$I->uncheckOption('#'.$fieldNameParts[1]);
					}
				} else if ($fieldNameParts[0]=='select' && count($fieldNameParts)>1) {
					$I->selectOption('#'.$fieldNameParts[1] ,$fieldValue);
				} else if ($fieldNameParts[0]=='date' && count($fieldNameParts)>1) {
					//$I->selectOption('#'.$fieldNameParts[1] ,$fieldValue);
				} else if ($fieldNameParts[0]=='rte' && count($fieldNameParts)>1) {
					//$I->switchToIFrame('.cke_wysiwyg_frame');
					//$I->executeJS('
					//document.getElementsByClassName("cke_editable").foreach (function(key,value) {
				//		value.innerHTML = "<p>'.$fieldValue.'</p>";
				//	})
					//');
					//$I->switchToIFrame(); // back to parent
					//$I->executeJS('$("#'.$fieldNameParts[1].'").val("'.$fieldValue.'")');
					//$I->selectOption('#'.$fieldNameParts[1] ,$fieldValue);
				} else if ($fieldNameParts[0]=='autocomplete' && count($fieldNameParts)>1) {
					$I->fillField("#".$fieldNameParts[1],$fieldValue);
					$I->click($fieldValue,'.ui-autocomplete');
				} else {
					$I->fillField('#'.$fieldName ,$fieldValue);
				}
			}
		}
	}
	/**
	 * debug
	 */ 
	public function dumpSelector($I,$sel1) {
		// .tablesorter tbody tr
		$usernames=$I->grabMultiple($sel1);
		codecept_debug($usernames);
		codecept_debug(array('DONE'=>'mehere'));
	}
	
	/**
	 * Login to CMFIVE
	 */
    public function login($I,$username,$password) {
		$I->wantTo('Log in');
		$I->amOnPage('/auth/login');
		// skip form filling if already logged in
		if (strpos('/auth/login',$I->grabFromCurrentUrl())!==false) {
			$I->fillField('login',$username);
			$I->fillField('password',$password);
			$I->click('Login');
			//$redirect=$I->grabFromDatabase('user','redirect_url',array('login'=>$username));
			//if (strlen(trim($redirect)>0)) $I->canSeeInCurrentUrl($redirect);
		}
	}
	
	/**
	 * Logout from CMFIVE
	 */	
	public function logout($I) {
		//$I->click('Logout');
		$I->amOnPage('/auth/logout');
	}
	
	
		
	/****************************
	 * USERS
	 ****************************/
	/**
	 * Create a new user
	 */
	public function createUser($I,$username,$password,$firstName,$lastName,$email) {
		$I->click('List Users');
		$I->click('Add New User');
		$I->fillField('#login',$username);
		$I->fillField('#password',$password);
		$I->fillField('#password2',$password);
		$I->checkOption('#is_active');
		$I->fillField('#firstname',$firstName);
		$I->fillField('#lastname',$lastName);
		$I->fillField('#email',$email);
		$I->click('Save');
		$I->see('User '.$username.' added');
	}
	
	/**
	 * Delete a user matching $username
	 */
	public function deleteUser($I,$username) {
		$actionCompleted=false; // make sure we do find the user
		$I->click('List Users');
		$usernames=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($usernames))  {
			foreach ($usernames as  $k=>$u) {
				if (trim($u)==trim($username)) {
					$index=$k + 1;
					$deleteButton=".tablesorter tbody tr:nth-child(". $index .")";
					// disable confirm
					$I->executeJS('window.confirm = function(){return true;}');
					//codecept_debug('username match on DeleTE - '.$deleteButton);
					$I->click('Delete',$deleteButton);
					//$I->acceptPopup();
					$I->see('User '.$username.' deleted');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}
	
	/**
	 * Set permissions a user matching $username
	 */
	public function setUserPermissions($I,$username,$permissions) {
		$actionCompleted=false;  // make sure we do find the user
		$I->click('List Users');
		$usernames=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($usernames))  {
			foreach ($usernames as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($username)) {
					$I->click("Permissions",$button);
					$I->uncheckOption('input[type="checkbox"]');
					if (is_array($permissions)) {
						foreach ($permissions as $permission) {
							$I->checkOption('#check_'.$permission);
						}
					}
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}
	 
	/**
	 * Update a user matching $username
	 */
	public function updateUser($I,$username,$data) {
		$actionCompleted=false;  // make sure we do find the user
		$I->click('List Users');
		$usernames=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($usernames))  {
			foreach ($usernames as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($username)) {
					$I->click("Edit",$button);
					$this->fillForm($I,$data);
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	} 
		
	/****************************
	 * USERGROUPS
	 ****************************/
	/**
	 * Create a new user group
	 */
	public function createUserGroup($I,$title) {
		$I->click('List Groups');
		$I->click('New Group');
		$I->fillField('#title',$title);
		$I->click('Save');
		$I->see('New group added');
		$I->seeLink($title);
	}
	
	/**
	 * Delete a user group matching $title
	 */
	public function deleteUserGroup($I,$title) {
		$actionCompleted=false; // make sure we do find the user
		$I->click('List Groups');
		$titles=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($titles))  {
			foreach ($titles as  $k=>$u) {
				if (trim($u)==trim($title)) {
					$index=$k + 1;
					$button=".tablesorter tbody tr:nth-child(". $index .")";
					// disable confirm
					$I->executeJS('window.confirm = function(){return true;}');
					$I->click('Delete',$button);
					$I->see('Group is deleted');
					$I->dontSeeLink($title);
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}
	
	/**
	 * Update a user matching $username
	 */
	public function updateUserGroup($I,$oldTitle,$newTitle) {
		$actionCompleted=false;  // make sure we do find the user
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($oldTitle)) {
					$I->click($u);
					$I->fillField('#title',$newTitle);
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	} 
	
	/**
	 * Add a user to an existing user group
	 */
	public function addUserToUserGroup($I,$user,$userLabel,$userGroup,$isOwner='') {
		$actionCompleted=false;  // make sure we do find the user
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($userGroup)) {
					$I->click('More Info',$button);
					$I->click('New Member');
					$I->selectOption('#member_id',$userLabel);
					if ($isOwner)  $I->checkOption('#is_owner');
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	} 
	/**
	 * Add a user to an existing user group
	 */
	public function removeUserFromUserGroup($I,$user,$userLabel,$userGroup) {
		$actionCompleted=false;  // make sure we do find the user
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($userGroup)) {
					$I->click('More Info',$button);
					$users=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
					if (is_array($users))  {
						foreach ($users as  $uk=>$user) {
							$index=$uk + 1;
							$button=".tablesorter tbody tr:nth-child(". $index .")";
							if (trim($user)==trim($userLabel)) {
								// disable confirm
								$I->executeJS('window.confirm = function(){return true;}');
								$I->click('Delete');
								$actionCompleted=true;
							}
						}
					}
				}
			}
		}
		$I->assertTrue($actionCompleted);
	} 


	/**
	 * Set permissions a user matching $username
	 */
	public function setUserGroupPermissions($I,$userGroup,$permissions) {
		$actionCompleted=false;  // make sure we do find the user
		$I->click('List Groups');
		$groups=$I->grabMultiple('.tablesorter tbody tr td:nth-child(1)');
		//codecept_debug(array('got'=>'users'));
		//codecept_debug($usernames);
		if (is_array($groups))  {
			foreach ($groups as  $k=>$u) {
				$index=$k + 1;
				$button=".tablesorter tbody tr:nth-child(". $index .")";
				if (trim($u)==trim($userGroup)) {
					$I->click("More Info",$button);
					$I->click("Edit Permissions");
					$I->uncheckOption('input[type="checkbox"]');
					if (is_array($permissions)) {
						foreach ($permissions as $permission) {
							$I->checkOption('#check_'.$permission);
						}
					}
					$I->click('Save');
					$actionCompleted=true;
				}
			}
		}
		$I->assertTrue($actionCompleted);
	}
	
	
		
	/****************************
	 * TASK GROUPS
	 ****************************/
		
	/****************************
	 * Create a Task Group
	 ****************************/
	public function createTaskGroup($I,$taskGroup,$data) {
		$I->click('Task Groups');
		$I->click('New Task Group');
		$fields=[];
		$fields['select:task_group_type']=$data['task_group_type'];
		$fields['title']=$taskGroup;
		if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
		if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
		if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
		if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
		if (!empty($data['description'])) $fields['rte:description']=$data['description'];
		if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
		$this->fillForm($I,$fields);
		$I->click('Save');
		$I->see('Task Group '.$taskGroup.' added');
	}

	public function updateTaskGroup($I,$taskGroup,$data) {
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			$I->click('Edit Task Group');
			$fields=[];
			if (!empty($data['title'])) $fields['title']=$data['title'];
			if (!empty($data['can_assign'])) $fields['select:can_assign']=$data['can_assign'];
			if (!empty($data['can_view'])) $fields['select:can_view']=$data['can_view'];
			if (!empty($data['can_create'])) $fields['select:can_create']=$data['can_create'];
			if (!empty($data['is_active'])) $fields['select:is_active']=$data['is_active'];
			if (!empty($data['description'])) $fields['rte:description']=$data['description'];
			if (!empty($data['default_assignee_id'])) $fields['select:default_assignee_id']=$data['default_assignee_id'];
			$this->fillForm($I,$fields);
			$I->click('Update');
			$actionCompleted=true;	
		}
		$I->assertTrue($actionCompleted);
	}
	
	public function deleteTaskGroup($I,$taskGroup) {
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			//$I->click('Delete Task Group');
			$I->executeJS('$("#members button:nth-child(4)").click();');
			$I->executeJS('$("#cmfive-modal .savebutton").click();');
			//$I->click('Delete');
			$actionCompleted=true;	
		}
		$I->assertTrue($actionCompleted);
	}
	
	public function addMemberToTaskGroup($I,$taskGroup,$userLabel,$role) {
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			$I->click('Add New Members');
			$I->selectOption('#role',$role);
			$I->selectOption('#member',$userLabel);
			// ?? click doesn't seem to work in this modal form ??
			//$I->click('Submit');
			//$I->click('button.savebutton');
			$I->executeJS('$("#cmfive-modal form").submit();');
			$actionCompleted=true;	
		}
		$I->assertTrue($actionCompleted);
	}
	
	public function updateMemberInTaskGroup($I,$taskGroup,$userLabel,$role) {
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			if ($userRowNumber=$this->findTableRowMatching($I,1,$userLabel) > 0) {
				$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
				$I->click('Edit',$context);
				$I->selectOption('#role',$role);
				$I->click('Update');
				$actionCompleted=true;	
			}
		}
		$I->assertTrue($actionCompleted);
	}
	public function removeMemberFromTaskGroup($I,$taskGroup,$userLabel) {
		$I->click("Task Groups");
		$actionCompleted=false;	
		if ($rowNumber=$this->findTableRowMatching($I,1,$taskGroup) > 0)  {
			$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
			$I->click($taskGroup,$context);
			if ($userRowNumber=$this->findTableRowMatching($I,1,$userLabel) > 0) {
				$context=".tablesorter tbody tr:nth-child(". $rowNumber .")";
				$I->click('Delete',$context);
				$I->executeJS('$("#cmfive-modal .savebutton").click();');
				//$I->click('Delete');
				$actionCompleted=true;	
			}
		}
		$I->assertTrue($actionCompleted);
	}
	public function updateTaskGroupNotifications($I,$taskGroup,$notifications) {}
	
	
	
	/****************************
	 * TASKS
	 ****************************/
	
	/****************************
	 * Create a task
	 ****************************/
	public function createTask($I,$taskGroup,$task,$data) {
		$I->click('New Task');
		$this->fillForm($I,[
			'autocomplete:task_group_id'=>$taskGroup,
			'select:task_type'=>!empty($data['task_type']) ? $data['task_type'] : '',
			'title'=>$task,
			'select:status'=>!empty($data['status']) ? $data['status'] : '',
			'select:priority'=>!empty($data['priority']) ? $data['priority'] : '',
			//'date:dt_due'=>$data['dt_due'],
			'select:assignee_id'=>!empty($data['assignee_id']) ? $data['assignee_id'] : '',
			'estimate_hours'=>!empty($data['estimate_hours']) ?  $data['estimate_hours'] : '',
			'effort'=>!empty($data['effort']) ? $data['effort'] : '',
			'rte:description'=>!empty($data['description']) ?  $data['description'] : '',
		]);
		$I->click('Save');
	}
	
	
}

