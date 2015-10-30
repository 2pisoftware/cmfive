<?php 
namespace Helper;

/******************************************
 * Shared helper functions for working with cmFive
 * all public methods declared in helper class will be available in $I
 ******************************************/
class CmFiveTestHelper extends \Codeception\Module
{

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
				} else {
					$I->fillField('#'.$fieldName ,$fieldValue);
				}
			}
		}
	}
	 
	public function dumpSelector($I,$sel1) {
		// .tablesorter tbody tr
		$usernames=$I->grabMultiple($sel1);
		codecept_debug($usernames);
		codecept_debug(array('DONE'=>'mehere'));
	}
	
	
	
}

