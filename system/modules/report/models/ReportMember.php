<?php

class ReportMember extends DbObject {
	
	public $report_id;
	public $user_id;
	public $role;
	
	public static $_db_table = "report_member";
	
	public function getReport() {
		return $this->getObject("Report", $this->report_id);
	}
	
	public function getUser() {
		return $this->getObject("User", $this->user_id);
	}
	
}