<?php
///////////////////////////////////////////////////////////////////////////////
//
//                        Database Session Handling
//
///////////////////////////////////////////////////////////////////////////////
class SessionManager extends DbService {

	public $life_time;

	function __construct(Web $w) {
		parent::__construct($w);

		// Read the maxlifetime setting from PHP
		$this->life_time = get_cfg_var("session.gc_maxlifetime");

		// Register this object as the session handler
		session_set_save_handler(
		array( $this, "open" ),
		array( $this, "close" ),
		array( $this, "read" ),
		array( $this, "write"),
		array( $this, "destroy"),
		array( $this, "gc" )
		);

	}

	function open( $save_path, $session_name ) {
		global $sess_save_path;
		$sess_save_path = $save_path;
		return true;
	}

	function close() {
		return true;
	}

	function read( $id ) {

		// Set empty result
		$data = '';

		// Fetch session data from the selected database

		$time = time();

		$newid = addslashes($id);
		$sql = "SELECT `session_data` FROM `sessions` WHERE
				`session_id` = '$id' AND `expires` > $time";

		$rs = $this->_db->sql($sql)->fetch_all();

		if($rs) {
			$data = $rs[0]['session_data'];
		}

		return $data;

	}

	function write( $id, $data ) {

		// Build query
		$time = time() + $this->life_time;

		$newid = addslashes($id);
		$newdata = addslashes($data);

		$sql = "REPLACE `sessions`
			(`session_id`,`session_data`,`expires`) VALUES('$newid',
				'$newdata', $time)";

		$this->_db->sql($sql)->execute();

		return TRUE;

	}

	function destroy( $id ) {

		// Build query
		$newid = addslashes($id);
		$sql = "DELETE FROM `sessions` WHERE `session_id` =
				'$newid'";

		$this->_db->sql($sql)->execute();

		return TRUE;

	}
	function gc() {

		// Garbage Collection

		// Build DELETE query.  Delete all records who have passed the expiration time
		$sql = 'DELETE FROM `sessions` WHERE `expires` <
				UNIX_TIMESTAMP();';

		$this->_db->sql($sql)->execute();

		// Always return TRUE
		return true;

	}

}