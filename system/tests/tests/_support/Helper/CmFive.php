<?php 
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I
class CmFive extends \Codeception\Module
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
	
	public function logout($I) {
		//$I->click('Logout');
		$I->amOnPage('/auth/logout');
	}
	
	/**
	 * Create a new record with default parameters from test object
	 */
	public function createNewRecord($I,$test) {
		$this->doCreateNewRecord($I,$test->navSelector,'.addbutton',$test->createdMessage,$test->databaseTable, $test->validRecord, $test->validDBRecord);
		// TODO return id of new record
	}
	
	/**
	 * Create a new record
	 */
	public function doCreateNewRecord($I,$navSelector,$createButtonSelector,$saveText,$databaseTable,$record,$dbRecord) {
		$I->wantTo('Create a new record');
		$I->click($navSelector);
		$I->click($createButtonSelector);
		$r=array();
		if (array_key_exists('input',$record)) {
			foreach($record['input'] as $field=>$value) {
				$I->fillField($field,$value);
				$r[$field]=$value;
			}
		}
		if (array_key_exists('select',$record)) {
			foreach($record['select'] as $field=>$value) {
				$I->selectOption($field,$value);
				$r[$field]=$value;
			}
		}
		if (array_key_exists('checkbox',$record)) {
			foreach($record['checkbox'] as $field=>$value) {
				if ($value=='1') {
					$I->checkOption($field);
				} else {
					$I->uncheckOption($field);
				}
				$r[$field]=$value;
			}
		}
		
		$I->click('.savebutton');
		$I->see($saveText);
		$I->seeInDatabase($databaseTable,$dbRecord);
	}
	
	/**
	 * Edit a record with default parameters from test object
	 */
	public function editRecord($I,$test) {
		$this->doEditRecord($I,$test->navSelector,'.editbutton[href="'.$test->moduleUrl.'edit/{id}/box"]',$test->updatedMessage,$test->databaseTable, $test->validRecord,$test->updateData, $test->validDBRecord,$test->updatedDBRecord);
	}
	
	/**
	 * Edit a record
	 */
	public function doEditRecord($I,$navSelector,$editButtonSelector,$saveText,$databaseTable,$record,$updateData,$dbRecord,$updatedDBRecord) {
		$I->wantTo('Edit and save a record');
		$r=array();
		$id=$I->haveInDatabase($databaseTable,$dbRecord);
		$editButtonSelector=str_replace('{id}',$id,$editButtonSelector);
		$I->click($navSelector);
		$I->click($editButtonSelector);
		if (array_key_exists('input',$updateData)) {
			foreach($updateData['input'] as $field=>$value) {
				$I->fillField($field,$value);
			}
		}
		if (array_key_exists('select',$updateData)) {
			foreach($updateData['select'] as $field=>$value) {
				$I->selectOption($field,$value);
			}
		}
		if (array_key_exists('checkbox',$updateData)) {
			foreach($updateData['checkbox'] as $field=>$value) {
				if ($value=='1') {
					$I->checkOption($field);
				} else {
					$I->uncheckOption($field);
				}
				$r[$field]=$value;
			}
		}
		
		$I->click('.savebutton');
		$I->see($saveText);
		$I->seeInDatabase($databaseTable, $updatedDBRecord); 
	}
	
	/**
	 * Delete a record with default parameters from test
	 */
	public function deleteRecord($I,$test) {
		$id=$this->doDeleteRecord($I,
			$test->navSelector, // nav to page
			$test->moduleUrl.'delete/',  // delete link base
			$test->deletedMessage,  // success message
			$test->databaseTable,  // table 
			$test->validDBRecord);  // dummy record
	}  
	
	/**
	 * Delete a record
	 */
	public function doDeleteRecord($I,$navSelector,$deleteButtonUrl,$deletedText,$databaseTable,$record) {
		$I->wantTo('Delete a record');
		$id=$I->haveInDatabase($databaseTable, $record);
		$I->click($navSelector);
		$I->click('.deletebutton[href="'.$deleteButtonUrl.$id.'"]');
		$I->see($deletedText);
		//$I->seeInDatabase($databaseTable, array('id' => $id,'is_deleted'=>'1'));
		return $id;
	}  
	
	/**
	 * Run search tests
	 */ 
	public function searchRecords($I,$test) {
		$this->doSearchRecords($I,$test->navSelector,$test->searches);
	}
	public function doSearchRecords($I,$navSelector,$searches) {
		$I->wantTo('Search '.$navSelector);
		$I->click($navSelector);
		$I->runSearches($I,$searches);
	}
	/**
	 * Run a search with criteria and check number of results for each element of searches array 
	 */ 
	public function runSearches($I,$searches) {
		foreach ($searches as $k=> $searchCriteria) {
			if (array_key_exists('input',$searchCriteria)) {
				foreach ($searchCriteria['input'] as $field => $value) {
					$I->fillField("#".$field,$value);
				}
			}
			if (array_key_exists('select',$searchCriteria)) {
				foreach ($searchCriteria['select'] as $field => $value) {
					$I->selectOption("#".$field,$value);
				}
			}
			if (array_key_exists('checkbox',$searchCriteria)) {
				foreach ($searchCriteria['checkbox'] as $field => $value) {
					if ($value=='1') {
						$I->checkOption($field);
					} else {
						$I->uncheckOption($field);
					}
				}
			}
			$I->click('Filter');
			$I->seeNumberOfElements('table.tablesorter tbody tr',$searchCriteria['result']);
		}
	}

	
}

