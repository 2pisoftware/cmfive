<?php
// report member object
class ReportMember extends DbObject {
	var $report_id;		// report id
	var $user_id; 		// user id
	var $role;			// user role: user, editor
	var $is_deleted; 	// deleted flag

	public static $_db_table = "report_member";
	// actual table name
	public function getDbTableName() {
		return "report_member";
	}
}