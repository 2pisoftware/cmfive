<?php
class ReportFeed extends DBObject {
	public $report_id;		// source report id
	public $title;			// feed title
	public $description;	// feed description
	public $key;			// special feed key
	public $url;			// url to access feed
	public $dt_created;	// date created
	public $is_deleted;	// is deleted flag

	// actual table name
	function getDbTableName() {
		return "report_feed";
	}

	// get feed key upon insert of new feed
	function insert() {
		if (!$this->key)
		$this->key = uniqid();

		// insert feed into database
		parent::insert();
	}
}