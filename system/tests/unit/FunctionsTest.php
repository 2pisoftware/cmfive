<?php
use \Codeception\Util\Stub;
	
class FunctionsTest extends  \Codeception\TestCase\Test {
	
	
	/**
	* @var \UnitGuy
	*/
	protected $guy;

	
	public function _before() {
	}
	
	
	/*****************************************
	 * TESTS
	 *****************************************/
	

/*

function in_multiarray($value, $array) {
function in_modified_multiarray($value, $array, $levels = 3) {
function getBetween($content, $start, $end) {
function is_associative_array($array) {
function is_complete_associative_array($array) {
function in_numeric_range($subject, $min, $max, $include = true) {

function paginate($array, $pageSize) {
function columnize($array, $noOfColumns) {
function strcontains($haystack, $needle_array) {
function startsWith($haystack, $needle) {
function array_unique_multidimensional($input) {
function recursiveArraySearch($haystack, $needle, $index = null) {

*/

function test_humanReadableBytes() {
	codecept_debug(humanReadableBytes(null));
	codecept_debug(humanReadableBytes(-5000));
	codecept_debug(humanReadableBytes(5000,4));
	// bytes value 1000
	codecept_debug(humanReadableBytes(5000,4,false));
	$this->assertEquals(humanReadableBytes(5000000000000000),'4547.47 TB');
	$this->assertEquals(humanReadableBytes(5000000000000),'4.55 TB');
	$this->assertEquals(humanReadableBytes(5000000000),'4.66 GB');
	$this->assertEquals(humanReadableBytes(5000000),'4.77 MB');
	$this->assertEquals(humanReadableBytes(5000),'4.88 KB');
}

function test_toSlug() {
	
	$this->assertTrue(toSlug('This is my name')==='this-is-my-name');
	$this->assertTrue(toSlug('This_is,my/.name')==='this-is-my--name');
}

function test_getFileExtension() {
	// from list of types
	$this->assertTrue(getFileExtension('text/javascript')=='.js');
	// as fallback 
	$this->assertTrue(getFileExtension('somewierd/mimetype')=='.mimetype');
}

function test_encryption() {
	$text='this is what i want to hide';
	$password='thepasswordthatiwantotdostuffwit';
	$encrypted=AESencrypt($text, $password);
	$decrypted=AESdecrypt($encrypted, $password);
	// really did change
	$this->assertFalse($encrypted===$text);
	// full cycle OK
	$this->assertTrue($decrypted===$text);	
}





/*******************************************
 * NOT TESTED
 *******************************************/
/*
function formatDate($date, $format = "d/m/Y", $usetimezone = true) {
function formatDateTime($date, $format = "d/m/Y h:i a", $usetimezone = true) {
function formatMoney($format, $number) {


function isNumber($var) {
function defaultVal($val, $default = null, $forceNull = false) {
function rotateImage($img, $rotation) {

function lookupForSelect(&$w, $type) {
function getStateSelectArray() {
function getTimeSelect($start = 8, $end = 19) {
function returncorrectdates(Web &$w, $dm_var, $from_date, $to_date) {

function str_whitelist($dirty_data, $limit = 0) {
function phone_whitelist($dirty_data) {
function int_whitelist($dirty_data, $limit) {
*/


}
