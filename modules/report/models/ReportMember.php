<?php
// report member object
class ReportMember extends DbObject {
	var $report_id;		// report id
	var $user_id; 		// user id
	var $role;			// user role: user, editor
	var $is_deleted; 	// deleted flag

	// actual table name
	function getDbTableName() {
		return "report_member";
	}
}