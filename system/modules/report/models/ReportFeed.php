<?php
class ReportFeed extends DBObject {
	var $report_id;		// source report id
	var $title;			// feed title
	var $description;	// feed description
	var $key;			// special feed key
	var $url;			// url to access feed
	var $dt_created;	// date created
	var $is_deleted;	// is deleted flag

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