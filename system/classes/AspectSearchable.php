<?php
/**
 * Use this aspect to create automatic full text indexing
 * for any object marked with this aspect.
 * 
 * Switch this on by creating a property called
 * 
 *    var $_searchable;
 * 
 * To exclude certain properties from being indexed:
 * 
 *    var $_exclude_index = array("prop1","prop2");
 * 
 * The old style modification properties will be automatically ignored:
 * 
 *    dt_created, dt_modified, creator_id, modifier_id
 * 
 * Also any property that ends with _id will be ignored.
 * 
 * Volatile properties that start with $_ will be ignored
 * 
 * It is possible to add custom content to the indexed string. Just create a method:
 * 
 * function addToIndex() {}
 * 
 * Which should return the string that needs to be added to the indexed content. 
 * All sanitising and de-duplication will be performed on that string as well.
 * 
 * @author carsten@tripleacs.com 2013
 *
 */
class AspectSearchable {
	private $object;
	private $_index;
	
	// a list of english words that need not be searched against
	// and thus do not need to be stored in an index
	var $stopwords = "about above across after again against all almost alone along already also although always among and any anybody anyone anything anywhere are area areas around ask asked asking asks away back backed backing backs became because become becomes been before began behind being beings best better between big both but came can cannot case cases certain certainly clear clearly come could did differ different differently does done down downed downing downs during each early either end ended ending ends enough even evenly ever every everybody everyone everything everywhere face faces fact facts far felt few find finds first for four from full fully further furthered furthering furthers gave general generally get gets give given gives going good goods got great greater greatest group grouped grouping groups had has have having her here herself high higher highest him himself his how however important interest interested interesting interests into its itself just keep keeps kind knew know known knows large largely last later latest least less let lets like likely long longer longest made make making man many may member members men might more most mostly mrs much must myself necessary need needed needing needs never new newer newest next nobody non noone not nothing now nowhere number numbers off often old older oldest once one only open opened opening opens order ordered ordering orders other others our out over part parted parting parts per perhaps place places point pointed pointing points possible present presented presenting presents problem problems put puts quite rather really right room rooms said same saw say says second seconds see seem seemed seeming seems sees several shall she should show showed showing shows side sides since small smaller smallest some somebody someone something somewhere state states still such sure take taken than that the their them then there therefore these they thing things think thinks this those though thought thoughts three through thus today together too took toward turn turned turning turns two under until upon use used uses very want wanted wanting wants was way ways well wells went were what when where whether which while who whole whose why will with within without work worked working works would year years yet you young younger youngest your yours";
	
	function __construct(DbObject &$obj) {
		$this->object = $obj;
	}
	
	private function & getIndex() {
		if ($this->object && $this->object->id && !$this->_index) {
			$this->_index = $this->object->getObject("ObjectIndex", 
					array("class_name"=>get_class($this->object), 
							"object_id"=>$this->object->id));
		}
		return $this->_index;
	}
	
	/**
	 * Create index entry for new objects
	 */
	function insert() {
		if (!$this->getIndex()) {
			$io = new ObjectIndex($this->object->w);
			$io->class_name = get_class($this->object);
			$io->object_id = $this->object->id;
			$io->dt_created = time();
			$io->creator_id = $io->Auth->user()->id;
			
			$io->content = $this->getIndexContent();
			
			$io->insert();
		}
	}
	
	/**
	 * Update index for updated object
	 */
	function update() {
		if ($this->getIndex()) {
			$this->_index->dt_modified = time();
			$this->_index->modifier_id = $this->_index->w->Auth->user()->id;
			
			$this->_index->content = $this->getIndexContent();
					
			$this->_index->update();
		} else {
			$this->insert();
		}
	}
	
	/**
	 * Delete index entry for deleted objects
	 * 
	 * The object may only be marked as deleted, but nevertheless it should not be used any more
	 * for searches!
	 */
	function delete() {
		if ($this->getIndex()) {
			$this->_index->delete();
		}
	}
	
	/**
	 * Consolidate all object fields into one big search friendly string.
	 * 
	 */
	function getIndexContent() {
		
		// -------------- concatenate all object fields ---------------------
		
		$str = "";
		$exclude = array("dt_created", "dt_modified", "id", "w");
		
		foreach (get_object_vars($this->object) as $k => $v) {
			if ($k{0} != "_" // ignore volatile vars
				&& (! property_exists($this->object,"_exclude_index") // ignore properties that should be excluded
					|| !in_array($k, $this->object->_exclude_index))
				&& stripos($k, "_id") === false
				&& !in_array($k, $exclude)
			) 
			{
				$str .= $v ." ";
			}
		}
				
		// add custom content to the index
		if (method_exists($this->object,"addToIndex")) {
			$str .= $this->object->addToIndex();
		}
		
		// ------------ sanitise string ----------------------------------
		
		// Remove all xml/html tags
		$str = strip_tags($str);
		
		// Remove case
		$str = strtolower($str);
		
		// Remove line breaks
		$str = str_replace("\n", " ", $str);
		
		// Remove all characters except A-Z, a-z, 0-9, dots, commas, hyphens, spaces and forward slashes (for dates) 
		// Note that the hyphen must go last not to be confused with a range (A-Z) 
		// and the dot, being special, is escaped with backslash
		$str = preg_replace("/[^A-Za-z0-9 \.,\-\/@':]/", '', $str);  
		
		// Replace sequences of spaces with one space 
		$str = preg_replace('/  +/', ' ', $str);
			
		// de-duplicate string and remove any word shorter than 3 characters
		$temparr = array_filter(array_unique(explode(" ", $str)),function ($item) { return strlen($item) >= 3; });
		
		// remove stop words
		$temparr = array_diff($temparr,explode(" ",$this->stopwords));
		$str = implode(" ", $temparr);
		
		return $str;
	}
	
}

class ObjectIndex extends DbObject {
	var $class_name;
	var $object_id;
	var $content;
	
	var $dt_created;
	var $dt_modified;
	var $creator_id;
	var $modifier_id;
	
	// do not audit this table!
	var $__use_auditing = false;
	
	function getDbTableName() {
		return "object_index";
	}	
}
