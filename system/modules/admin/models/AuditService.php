<?php
class AuditService extends DbService {

	/**
	 *
	 * Adds an entry to the audit table
	 *
	 * The blacklist is a simple array of the form:
	 * array(
	 * 		array("<module>","<action>"),
	 * 		array("<module>","<action>"),
	 * 		...
	 * )
	 *
	 * @param $blacklist
	 */
	function addAuditLogEntry($blacklist = null) {
		// if blacklist exists
		// then bail out if the current module and action
		// is in the list
		if ($blacklist) {
			foreach ($blacklist as $line) {
				if ($line[0] == $this->w->currentModule() &&
				($line[1] == $this->w->currentAction() || $line[1] == "*")) {
					return;
				}
			}
		}
		$log = new Audit($this->w);
		$log->module = $this->w->currentModule();
		$log->submodule = $this->w->currentSubModule();		
		$log->action = $this->w->currentAction();
		$log->path = $_SERVER['REQUEST_URI'];
		$log->ip = $this->w->requestIpAddress();
		$log->insert();
	}
	
	function addDbAuditLogEntry($action, $class, $id) {
		if ($class != "Audit") {
			$log = new Audit($this->w);
			$log->module = $this->w->currentModule();
			$log->submodule = $this->w->currentSubModule();
			$log->action = $this->w->currentAction();
			$log->path = $_SERVER['REQUEST_URI'];
			$log->ip = $this->w->requestIpAddress();
			$log->db_action = $action;
			$log->db_class = $class;
			$log->db_id = $id;
			$log->insert();
		}
	}
	
	
}