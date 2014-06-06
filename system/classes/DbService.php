<?php
/**
 * General Service class for subclassing in modules.
 *
 * @author carsten
 *
 */
class DbService {
	public $_db;
	public $w;

	/**
	 * array for automatic caching of objects for the duration of this
	 * invocation.
	 *
	 * @var <type>
	 */
	private static $_cache = array(); // used for single objects
	private static $_cache2 = array();  // used for lists of objects

	/**
	 * This variable keeps track of active transactions
	 *
	 * @var boolean
	*/
	private static $_active_trx = false;

	public function __get($name) {
		return $this->w->$name;
	}

	function __construct(Web $w) {
		$this->_db = $w->db;
		$this->w = $w;
	}

	/**
	 * Formats a timestamp
	 * per default MySQL datetime format is used
	 *
	 * @param $time
	 * @param $format
	 */
	function time2Dt($time=null,$format='Y-m-d H:i:s') {
		return formatDate($time ? $time : time(),$format,false);
	}

	/**
	 * Formats a timestamp
	 * per default MySQL date format is used
	 *
	 * @param $time
	 * @param $format
	 */
	function time2D($time=null,$format='Y-m-d') {
		return formatDate($time ? $time : time(),$format,false);
	}

	function time2T($time=null,$format='H:i:s') {
		return date($format,$time ? $time : time());
	}

	function dt2Time($dt) {
		return strtotime(str_replace("/","-",$dt));
	}

	function d2Time($d) {
		return strtotime(str_replace("/","-",$d));
	}

	function t2Time($d) {
		return strtotime(str_replace("/","-",$d));
	}

	/**
	 * Clear object cache completely!
	 *
	 */
	function clearCache() {
		self::$_cache = array();
		self::$_cache2 = array();
	}
	/**
	 * Fetch one object either by id
	 * or by passing an array of key,value
	 * to be used in a where condition
	 *
	 * @param <type> $table
	 * @param <type> $idOrWhere
	 * @return <type>
	 */
	function getObject($class,$idOrWhere,$use_cache = true,$order_by = null) {
		if (!$idOrWhere || !$class) return null;

		$key = $idOrWhere;
		if (is_array($idOrWhere)) {
			$key = "";
			foreach ($idOrWhere as $k=>$v) {
				$key.=$k."::".$v."::";
			}
		}
		$usecache = $use_cache && is_scalar($key);
		// check if we should use the cache
		// this will eliminate 80% of SQL calls per page view!
		if ($usecache) {
			$obj = !empty(self::$_cache[$class][$key]) ? self::$_cache[$class][$key] : null;
			if ($obj) {
				return $obj;
			}
		}

		// not using cache or object not found in cache

		$o = new $class($this->w);
		$table = $o->getDbTableName();

		if (is_scalar($idOrWhere)) {
			$this->_db->get($table)->where('id',$idOrWhere);
		} elseif (is_array($idOrWhere)) {
			$this->_db->get($table)->where($idOrWhere);
		}
        if (!empty($order_by)){
            $this->_db->order_by($order_by);
        }
        
        $result = $this->_db->fetch_row();
		if ($result) {
			$obj = $this->getObjectFromRow($class, $result);
			if ($usecache) {
				self::$_cache[$class][$key]=$obj;
				if ($obj->id != $key && !empty(self::$_cache[$class][$obj->id])) {
					self::$_cache[$class][$obj->id]=$obj;
				}
			}
			return $obj;
		} else {
			return null;
		}
	}

	/**
	 *
	 * @param String $class
	 * @param Mixed $where
	 * @param Boolean $useCache
	 * 
	 * @return <type>
	 */
	function getObjects($class,$where=null,$cache_list = false, $use_cache = true, $order_by = null) {
		if (!$class) return null;

		// if using the list cache
		if ($cache_list) {
			if (is_array($where)) {
                                $key = "";
				foreach ($where as $k=>$v) {
					$key .= $k."::".$v."::";
				}
			} else {
				$key = $where;
			}
	
			if (isset(self::$_cache2[$class][$key])) {
				return self::$_cache2[$class][$key];
			}
		}
		
		$o = new $class($this->w);
		$table = $o->getDbTableName();
		$this->_db->get($table);
		if ($where && is_array($where)) {
			foreach ($where as $par => $val) {
				$dbwhere[$o->getDbColumnName($par)]=$val;
			}
			$this->_db->where($dbwhere);
		} else if ($where && is_scalar($where)) {
			$this->_db->where($where,false);
		}
                if (!empty($order_by)){
                    $this->_db->order_by($order_by);
                }
		// echo $this->_db->getSql();
		$result = $this->_db->fetch_all();
		if ($result) {
			$objects = $this->fillObjects($class, $result);
			if ($objects) {
				 
				// store the complete list
				if ($cache_list && !isset(self::$_cache2[$class][$key])) {
					self::$_cache2[$class][$key] = $objects;
				}

				// also store each individual object
				if ($use_cache) {
					foreach ($objects as $ob) {
						if (!isset(self::$_cache[$class][$ob->id])) {
							self::$_cache[$class][$ob->id] = $ob;
						}
					}
				}
			}
			return $objects;
		} else {
			return null;
		}
	}

	/**
	 *
	 * @param <type> $table
	 * @param <type> $id
	 * @return <type>
	 */
	function getObjectFromRow($class, $row) {
		if (!$row || !$class) return null;
		$o = new $class($this->w);
		$o->fill($row);
		return $o;
	}

	function getObjectsFromRows($class, $rows) {
		$list = array();
		if (!empty($class) && !empty($rows) && class_exists($class)) {
			foreach($rows as &$row) {
				$list[] = $this->getObjectFromRow($class, $row);
			}
		}
		return $list;
	}

	// DEPRECATED AS OF 0.7.0
	function fillObjects($class, $rows) {
		return $this->getObjectsFromRows($class, $rows);
	}

	/**
	 * Start a transaction
	 *
	 */
	function startTransaction() {
		$this->_db->sql("START TRANSACTION")->execute();
		self::$_active_trx = true;
	}

	/**
	 * Commit a transaction
	 *
	 */
	function commitTransaction() {
		$this->_db->sql("COMMIT")->execute();
		self::$_active_trx = false;
	}

	/**
	 * Rollback a transaction!
	 * This includes a clear_sql()!
	 */
	function rollbackTransaction() {
		$this->_db->clear_sql();
		$this->_db->sql("ROLLBACK")->execute();
		self::$_active_trx = false;
	}

	/**
	 * Returns true if a transaction is currently active!
	 *
	 */
	function isActiveTransaction() {
		return self::$_active_trx;
	}


	function lookupArray($type) {
		$rows = $this->_db->select("code,title")->from("lookup")->where("type",$type)->fetch_all();
		foreach ($rows as $row) {
			$select[$row['code']]=$row['title'];
		}
		return $select;
	}

}

