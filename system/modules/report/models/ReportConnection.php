<?php
/**
 * This object stores connection data to other database sources
 * which can be used with reports.
 * 
 * @author careck
 *
 */
class ReportConnection extends DbObject {
	public $db_host;
	public $db_port;
	public $db_database;
	public $db_driver; // mysql, pgsql, oci, sqlsrv, odbc, sqlite
	public $s_db_user;
	public $s_db_password;
	
	private $_mydb;

	public function __construct(Web $w) {
		parent::__construct($w);
		$this->setPassword(hash("md5", $w->moduleConf("report", "__password")));
	}
	
	/**
	 * returns the database object for this connection
	 */
	public function getDb() {
            if (empty($this->_mydb)) {
	        $db_config = array(
	            'hostname' => $this->db_host,
                    'port' => $this->db_port,
	            'username' => $this->s_db_user,
	            'password' => $this->s_db_password,
	            'database' => $this->db_database,
	            'driver' => $this->db_driver,
	        );
                $this->_mydb = new DbPDO($db_config);
            }
            return $this->_mydb;
	}
	
	public function getSelectOptionTitle() {
		return $this->db_driver.":".$this->db_database."@".$this->db_host;
	}
	
}