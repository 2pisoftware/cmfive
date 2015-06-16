<<<<<<< HEAD
<?php
namespace AcceptanceTester;

class CM5WebGuySteps extends \AcceptanceTester
{
	/**
	 * Run all tests 
	 */  
	public function runCrudTests($test) {
		$this->runSQLQueries($test->subfolder);
		$this->login($test->username,$test->password);
		$this->searchRecords($test);
		$this->createNewRecord($test);
		$this->editRecord($test);
		$this->deleteRecord($test);
	}
	/**
	 * Login to CMFIVE
	 */
    public function login($username,$password) {
		$I=$this;
		$I->wantTo('Log in');
		$I->amOnPage('/auth/login');
		$I->fillField('login',$username);
		$I->fillField('password',$password);
		$I->click('Login');
		$redirect=$I->grabFromDatabase('user','redirect_url',array('login'=>$username));
		$I->canSeeCurrentUrlEquals("/".$redirect);
	}
	
	/**
	 * Create a new record with default parameters from test object
	 */
	public function createNewRecord($test) {
		$this->doCreateNewRecord($test->navSelector,'.addbutton',$test->createdMessage,$test->databaseTable, $test->validRecord, $test->validDBRecord);
		// TODO return id of new record
	}
	
	/**
	 * Create a new record
	 */
	public function doCreateNewRecord($navSelector,$createButtonSelector,$saveText,$databaseTable,$record,$dbRecord) {
		$I=$this;
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
	public function editRecord($test) {
		$this->doEditRecord($test->navSelector,'.editbutton[href="'.$test->moduleUrl.'edit/{id}/box"]',$test->updatedMessage,$test->databaseTable, $test->validRecord,$test->updateData, $test->validDBRecord,$test->updatedDBRecord);
	}
	
	/**
	 * Edit a record
	 */
	public function doEditRecord($navSelector,$editButtonSelector,$saveText,$databaseTable,$record,$updateData,$dbRecord,$updatedDBRecord) {
		$I=$this;
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
	public function deleteRecord($test) {
		$id=$this->doDeleteRecord(
			$test->navSelector, // nav to page
			$test->moduleUrl.'delete/',  // delete link base
			$test->deletedMessage,  // success message
			$test->databaseTable,  // table 
			$test->validDBRecord);  // dummy record
	}  
	
	/**
	 * Delete a record
	 */
	public function doDeleteRecord($navSelector,$deleteButtonUrl,$deletedText,$databaseTable,$record) {
		$I=$this;
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
	public function searchRecords($test) {
		$this->doSearchRecords($test->navSelector,$test->searches);
	}
	public function doSearchRecords($navSelector,$searches) {
		$I=$this;
		$I->wantTo('Search '.$navSelector);
		$I->click($navSelector);
		$I->runSearches($searches);
	}
	/**
	 * Run a search with criteria and check number of results for each element of searches array 
	 */ 
	public function runSearches($searches) {
		$I=$this;
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
=======
<?php
namespace AcceptanceTester;

class CM5WebGuySteps extends \AcceptanceTester
{
	/**
	 * Run all tests 
	 */  
	public function runCrudTests($test) {
		$this->runSQLQueries($test->subfolder);
		$this->login($test->username,$test->password);
		$this->searchRecords($test);
		$this->createNewRecord($test);
		$this->editRecord($test);
		$this->deleteRecord($test);
	}
	/**
	 * Login to CMFIVE
	 */
    public function login($username,$password) {
		$I=$this;
		$I->wantTo('Log in');
		$I->amOnPage('/auth/login');
		$I->fillField('login',$username);
		$I->fillField('password',$password);
		$I->click('Login');
		$redirect=$I->grabFromDatabase('user','redirect_url',array('login'=>$username));
		$I->canSeeCurrentUrlEquals("/".$redirect);
	}
	
	/**
	 * Create a new record with default parameters from test object
	 */
	public function createNewRecord($test) {
		$this->doCreateNewRecord($test->navSelector,'.addbutton',$test->createdMessage,$test->databaseTable, $test->validRecord, $test->validDBRecord);
		// TODO return id of new record
	}
	
	/**
	 * Create a new record
	 */
	public function doCreateNewRecord($navSelector,$createButtonSelector,$saveText,$databaseTable,$record,$dbRecord) {
		$I=$this;
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
	public function editRecord($test) {
		$this->doEditRecord($test->navSelector,'.editbutton[href="'.$test->moduleUrl.'edit/{id}/box"]',$test->updatedMessage,$test->databaseTable, $test->validRecord,$test->updateData, $test->validDBRecord,$test->updatedDBRecord);
	}
	
	/**
	 * Edit a record
	 */
	public function doEditRecord($navSelector,$editButtonSelector,$saveText,$databaseTable,$record,$updateData,$dbRecord,$updatedDBRecord) {
		$I=$this;
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
	public function deleteRecord($test) {
		$id=$this->doDeleteRecord(
			$test->navSelector, // nav to page
			$test->moduleUrl.'delete/',  // delete link base
			$test->deletedMessage,  // success message
			$test->databaseTable,  // table 
			$test->validDBRecord);  // dummy record
	}  
	
	/**
	 * Delete a record
	 */
	public function doDeleteRecord($navSelector,$deleteButtonUrl,$deletedText,$databaseTable,$record) {
		$I=$this;
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
	public function searchRecords($test) {
		$this->doSearchRecords($test->navSelector,$test->searches);
	}
	public function doSearchRecords($navSelector,$searches) {
		$I=$this;
		$I->wantTo('Search '.$navSelector);
		$I->click($navSelector);
		$I->runSearches($searches);
	}
	/**
	 * Run a search with criteria and check number of results for each element of searches array 
	 */ 
	public function runSearches($searches) {
		$I=$this;
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
>>>>>>> origin/windowsAdaptation
