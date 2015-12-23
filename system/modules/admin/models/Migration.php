<?php

class Migration extends DbObject {
	
	public $path;
	public $classname;
	public $module;
	public $dt_created;
	public $batch;
	
	public $_NEXT_BATCH;
	
	public function getNextBatchNumber() {
		if (empty($this->_NEXT_BATCH)) {
			$current_no = $this->w->db->get("migration")->select()->select("batch")->orderBy("batch DESC")->limit("1")->fetch_element("batch");
			$this->_NEXT_BATCH = !empty($current_no) ? $current_no + 1 : 1;
		}
		
		return $this->_NEXT_BATCH;
	}
}