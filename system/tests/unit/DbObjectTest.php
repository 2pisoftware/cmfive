<?php
use \Codeception\Util\Stub;
	
class DbObjectTest extends  \Codeception\TestCase\Test {
	
	
	/**
	* @var \UnitGuy
	*/
	protected $guy;
	
	public static $web;
	public static $dbService;
	
	public function _before() {
		self::$web = Stub::construct("Web",[],[]);
		self::$web->initDb();
		self::$dbService = new DbService(self::$web);
	}
	
	/*****************************************
	 * TESTS
	 *****************************************/
	function test_setPassword() {
		$noLabel= new TestmoduleFoodNoLabel(self::$web);
		$noLabel->setPassword('fredfredfredfred');
		$this->assertEquals(Config::get('system.password_salt'),'fredfredfredfred');
		// no change for false evaluation of value
		$noLabel->setPassword(false);
		$this->assertEquals(Config::get('system.password_salt'),'fredfredfredfred');
	}
	
	function test_decrypt() {
		$data= new TestmoduleData(self::$web);
		$data->setPassword('fredfredfredfred');
		$data->s_data=AESencrypt('mysecret', 'fredfredfredfred');
		//codecept_debug();
		$data->decrypt();
		$this->assertEquals($data->s_data,'mysecret');
	}
    
	function test_labelGenerators() {
		//function test_selectOptionTitle() {}
		//function test_getSelectOptionTitle() {}
		//function test_getSelectOptionValue() {}
		//function printSearchTitle() {
		//function printSearchListing() {
		//function printSearchUrl() {
		//public function __toString() {
		$noLabel= new TestmoduleFoodNoLabel(self::$web);
		$noLabel->id=999;
		$this->assertEquals($noLabel->__toString(),'TestmoduleFoodNoLabel[999]');
		$this->assertEquals($noLabel->printSearchTitle(),'TestmoduleFoodNoLabel[999]');
		$this->assertEquals($noLabel->printSearchListing(),'TestmoduleFoodNoLabel[999]');
		$this->assertNull($noLabel->printSearchUrl());
		$this->assertEquals($noLabel->getSelectOptionTitle(),'999');
		$hasTitle= new TestmoduleFoodHasTitle(self::$web);
		$hasTitle->title='my title';
		$this->assertEquals($hasTitle->getSelectOptionTitle(),'my title');
		$hasName= new TestmoduleFoodHasName(self::$web);
		$hasName->name='my name';
		$this->assertEquals($hasName->getSelectOptionTitle(),'my name');
	}
	
	function test_toLink() { //$class = null, $target = null, $user = null) {
		$data= new TestmoduleData(self::$web);
		$data->title='my title';
		$data->id=99;
		$user=new User(self::$web);
		$link=$data->toLink('myclass','_new',$user);
		//codecept_debug($link);
		$this->assertEquals($link,"<a href='http://localhost/' class='myclass' target='_new' >TestmoduleData[99]</a>");
		$data= new TestmoduleFoodNoLabel(self::$web);
		$data->title='my title';
		$data->id=99;
		$link=$data->toLink('myclass','_new',$user);
		//codecept_debug($link);
		$this->assertEquals($link,"TestmoduleFoodNoLabel[99]");
	}

    function test_readConvert() {
		$data= new TestmoduleData(self::$web);
		$this->assertEquals($data->readConvert('d_fred','12/4/2012'),$data->d2Time('12/4/2012'));
		$this->assertEquals($data->readConvert('dt_fred','12/4/2012'),$data->dt2Time('12/4/2012'));
		$this->assertEquals($data->readConvert('fred','eek'),'eek');
	} //$k, $v) {

    function test_updateConvert() {
		$data= new TestmoduleData(self::$web);
		//1334152800
		$this->assertEquals($data->updateConvert('d_fred','12/4/2012'),$data->time2D('12/4/2012'));
		$this->assertEquals($data->updateConvert('dt_fred','12/4/2012'),$data->time2Dt('12/4/2012'));
		$this->assertEquals($data->updateConvert('t_fred',99999999),$data->time2T(99999999));
		$this->assertEquals($data->updateConvert('s_fred','mysecret'),AESencrypt('mysecret', Config::get('system.password_salt')));
		// no conversion
		$this->assertEquals($data->updateConvert('fred','eek'),'eek');
	} //$k, $v) {
    
    function test_getObjectVars() {
		$data= new TestmoduleData(self::$web);
		$this->assertEquals($data->getObjectVars(),['title','data','d_last_known','t_killed','dt_born','s_data','id']);

	} //) {
 
	function test_fill() {
		$data= new TestmoduleData(self::$web);
		$data->fill(['data'=>'thedata','title'=> 'thetitle','notafield'=>'some ignored value','d_last_known'=>'65765766667']);
		$this->assertEquals($data->data,'thedata');
		$this->assertEquals($data->title,'thetitle');
		$this->assertEquals($data->d_last_known,'65765766667');
		$data->fill(['data'=>'thedata','title'=> 'thetitle','notafield'=>'some ignored value','d_last_known'=>'65765766667'],true);
		$this->assertEquals($data->d_last_known,$data->d2Time('65765766667'));
	} //$row, $convert = false) {}
	function test_copy() {} //$saveToDB = false) {}
	
	function test_toArray() {
		$data= new TestmoduleData(self::$web);
		$fill=['title'=>'the title','data'=>'the data','d_last_known'=>'the last date known','t_killed'=>'time killed','dt_born'=>'date born','s_data'=>'secret data','id'=>'the ID'];
		$data->fill($fill);
		$this->assertEquals($data->toArray(),$fill);
		//codecept_debug($data->toArray());
	}

	function test_getDbTableName() {
		//function _tn() {
    	$hasName=new TestmoduleFoodHasName(self::$web);
		$hasTitle=new TestmoduleFoodHasTitle(self::$web);
		$noLabel=new TestmoduleFoodNoLabel(self::$web);
		// class var
		$this->assertEquals($hasName->_tn(),'patch_testmodule_food_has_name');
		// static
		$this->assertEquals($hasTitle->_tn(),'patch_testmodule_food_has_title');
		// default from get_class munged
		$this->assertEquals($noLabel->_tn(),'testmodule_food_no_label');
	}
   
	function test_getDbTableColumnNames() {
		$hasName=new TestmoduleFoodHasName(self::$web);
		$hasTitle=new TestmoduleFoodHasTitle(self::$web);
		$noLabel=new TestmoduleFoodNoLabel(self::$web);
		
		$this->assertEquals($hasName->getDbTableColumnNames(),['id','name']);
		$this->assertEquals($hasTitle->getDbTableColumnNames(),['id','title']);
		$this->assertEquals($noLabel->getDbTableColumnNames(),['id','data']);
	}

	function test_getHumanReadableAttributeName() { //$attribute) {
		$data=new TestmoduleData(self::$web);
		$in=['d_jo','dt_jo','t_jo','t_dt_jo','customer_id','my_long_name'];
		$out=['Jo','Jo','Jo','Dt Jo','Customer','My Long Name'];
		foreach ($in as $key=>$attribute) {
			$this->assertEquals($data->getHumanReadableAttributeName($attribute),$out[$key]); 
		}
	}
	
    function test_getDbColumnName() { //$attr) {
		//function _cn($attr) {
		$data=new TestmoduleData(self::$web);
		// just returns incoming value
		$this->assertEquals($data->_cn('arandomvalue'),'arandomvalue');
	}



    function test_getIndexContent() {
		$data= new TestmoduleData(self::$web);
		$data->id='99';
		$data->title='thetitle';
		$data->data='thedata';
		codecept_debug($data->getIndexContent());
		$this->assertEquals($data->getIndexContent(),'thetitle thedata interestingly thern testmoduledata::99');
		// TODO IMPLEMENT HOOKS AND TRY THE $ignoreAdditional parameter
	} //$ignoreAdditional = true) {
    
    
	function test_dateTimeConversions() {
		//function getDate($var, $format = 'd/m/Y') {
		//function getDateTime($var, $format = 'd/m/Y H:i') {
		//function getTime($var, $format = null) {
		//function setTime($var, $date) {
		//function setDate($var, $date) {
		//function setDateTime($var, $date) {
	}
   
	
	function test_validate() {
		
	}
	
	function test_getSelectOptions() {
		$data= new TestmoduleData(self::$web);
		codecept_debug($data->getSelectOptions());
	} //$field) {

/*
    function insertOrUpdate($force_null_values = false, $force_validation = true) {
    function insert($force_validation = true) {
    function update($force_null_values = false, $force_validation = true) {
    function delete($force = false) {
    // tested by calls to insert,update,delete
    function test_callHooks($type, $action) {}
	
    
    // stubbing
    function getCreator() {
    function getModifier() {
   
   // ??  - aspects, 
    function __construct(Web &$w) {
    public function __clone(){
    public function __get($name) {
     
     
    
	
	* 
	* 
	// intended to be overridden
	function canList(User $user) {
    function canView(User $user) {
    function canEdit(User $user) {
    function canDelete(User $user) {
    function addToIndex() {
    
    
*/

}
