<?php
namespace WebGuy;

class CM5WebGuySteps extends \WebGuy
{
	/*
	 * Login to CMFIVE
	 */
    public function login($username,$password) {
		$I=$this;
		$I->amOnPage('/auth/login');
		$I->fillField('login',$username);
		$I->fillField('password',$password);
		$I->click('Login');
		$redirect=$I->grabFromDatabase('user','redirect_url',array('login'=>$username));
		$I->seeCurrentUrlEquals("/".$redirect);
	}
	
	/*
	 * Create a new record with default parameters from test object
	 */
	public function createNewRecord($test) {
		$this->doCreateNewRecord($test->navSelector,'.addbutton','Save',$test->recordLabel.' created',$test->databaseTable, $test->validRecord);
		// TODO return id of new record
	}
	
	/*
	 * Create a new record
	 */
	public function doCreateNewRecord($navSelector,$createButtonSelector,$saveButtonSelector,$saveText,$databaseTable,$record) {
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
		$I->click($saveButtonSelector);
		$I->see($saveText);
		$I->seeInDatabase($databaseTable,$r);
	}
	
	/*
	 * Edit a record with default parameters from test object
	 */
	public function editRecord($test) {
		$this->doEditRecord($test->navSelector,$test->moduleUrl.'edit/','Update',$test->recordLabel.' updated',$test->databaseTable, $test->validRecord,$test->updateData);
	}
	
	/*
	 * Edit a record
	 */
	public function doEditRecord($navSelector,$editButtonUrl,$saveButtonSelector,$saveText,$databaseTable,$record,$updateData) {
		$I=$this;
		$r=array();
		if (array_key_exists('select',$record)) $r=array_merge($r,$record['select']);
		if (array_key_exists('input',$record)) $r=array_merge($r,$record['input']);
		$id=$I->haveInDatabase($databaseTable,$r);
		if (array_key_exists('select',$updateData)) $r=array_merge($r,$updateData['select']);
		if (array_key_exists('input',$updateData)) $r=array_merge($r,$updateData['input']);
		$I->wantTo('Edit and save a record');
		$I->click($navSelector);
		$I->click('.editbutton[href="'.$editButtonUrl.$id.'"]');
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
		$I->click($saveButtonSelector);
		$I->see($saveText);
		//$I->seeInDatabase($databaseTable, $r); 
	}
	
	/*
	 * Delete a record with default parameters from test
	 */
	public function deleteRecord($test) {
		$id=$this->doDeleteRecord(
			$test->navSelector, // nav to page
			$test->moduleUrl.'delete/',  // delete link base
			$test->recordLabel.' deleted',  // success message
			$test->databaseTable,  // table 
			array_merge($test->validRecord['select'],$test->validRecord['input']));  // dummy record
	}  
	
	/*
	 * Delete a record
	 */
	public function doDeleteRecord($navSelector,$deleteButtonUrl,$deletedText,$databaseTable,$record) {
		$I=$this;
		$id=$I->haveInDatabase($databaseTable, $record);
		$I->wantTo('Delete a record');
		$I->click($navSelector);
		$I->click('.deletebutton[href="'.$deleteButtonUrl.$id.'"]');
		$I->see($deletedText);
		//$I->seeInDatabase($databaseTable, array('id' => $id,'is_deleted'=>'1'));
		return $id;
	}  
	
	/*
	 * Run search tests
	 */ 
	public function searchRecords($test) {
		$this->doSearchRecords($test->navSelector,$test->searches,'sql'.DIRECTORY_SEPARATOR.$test->subfolder.DIRECTORY_SEPARATOR."search");
	}
	public function doSearchRecords($navSelector,$searches,$sql) {
		$I=$this;
		$I->wantTo('Search '.$navSelector);
		$I->click($navSelector);
		$I->runSQLQueries($sql);
		$I->runSearches($searches);
	}
	/*
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
			$I->click('Filter');
			$I->seeNumberOfElements('table.tablesorter tr',$searchCriteria['result']);
		}
	}

}
