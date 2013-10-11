<?php
/**
 * PDO Extension class, methods used are to emulate methods called from Crystal DB.
 * See: http://www.php.net/manual/en/book.pdo.php for the PDO Class reference
 * 
 * @author Adam Buckley for TripleACS
 */
class DbPDO extends PDO {
    private static $table_names = array();
    private $query = null;
    private $fpdo = null;
    public $sql = null;
    public $meta = array("page" => 0, "per_page" => 0, "total_results" => 0);
    
    public function __construct($config = array()) {
        // Set up our PDO class
        $url = "{$config['driver']}:host={$config['hostname']};dbname={$config['database']}";
        parent::__construct($url,$config["username"],$config["password"], null);
        
        // Since you cant bind table names, maybe its a good idea to
        // load an array of table names to check against? But this is probably
        // unecessary to do on every call so maybe move it to get()
        // Setting this to static however should make this array share the memory
        // heap for this var across all instances
        if (empty(DbPDO::$table_names)){
            foreach($this->query("show tables")->fetchAll(PDO::FETCH_NUM) as $table)
                DbPDO::$table_names[] = $table[0];
        }
        // Instantiate a FluentPDO class and init vars
        $this->fpdo = new FluentPDO($this);
        $this->sql = $this->getSql();
    } 

    /**
     * This function sets up a FluentPDO query with the given table name, an
     * error will be thrown if the table name doesn't exist in the database
     * 
     * @param type $table_name
     * @return \DbPDO|null
     */
    public function get($table_name){
        if (!in_array($table_name, DbPDO::$table_names)){
            trigger_error("Table does not exist in the databse", E_USER_ERROR);
            return null;
        }  
        $this->query = $this->fpdo->from($table_name);
        // $this->statement = "SELECT * FROM " . $table_name . " ";// $this->prepare("SELECT * FROM " . $table_name);
        return $this;
    }
    
    /**
     * This function appends where clauses to the query, the where part of the
     * statement can be reset by passing NULL as the first parameter
     * 
     * @param String|Array $column
     * @param String $equals
     * @return \DbPDO|null
     */
    public function where($column, $equals = null){
        if ($this->query !== null){
            if (empty($column)){
                // Resets the where part of the statement
                $this->query = $this->query->where(null);
            } else {
                if (is_array($column)){
                    $this->query = $this->query->where($column);
                } else {
                    $this->query = $this->query->where($column, $equals);
                }
            }
            return $this;
        }
        return null;
    }
    
    /**
     * Prepares a statement with a query
     * Note that in the migration from Crystal, the sql function executed RAW SQL
     * Which is what this is emulating
     * 
     * @param String $query
     * @return DbPDO
     */
    public function sql($query){
        $this->query = $this->prepare($query);
        return $this;
    }
    
    /**
     * Executes a prepared statement
     * 
     * @return Result
     */
    public function execute(){
        $this->query = $this->query->execute();
        return $this->query;
    }
    
    /**
     * A grace method for our migration from Crystal
     * Crystal used "fetch_row" whereas PDO uses "fetch"
     * 
     * @return Result set
     */
    public function fetch_row() {
        return $this->query->fetch();
    }
    
    /**
     * A grace method for our migration from Crystal
     * Crystal used "fetch_all" whereas PDO uses "fetchAll"
     * 
     * @return Result set
     */
    public function fetch_all(){
        return $this->query->fetchAll();
    }
    
    /**
     * Sets up class with a PDO insert query and required array of values
     * 
     * @param String $table_name Name of data table
     * @param Array $data Data to insert
     * @return \DbPDO
     */
    public function insert($table_name, $data){
        $this->query = $this->fpdo->insertInto($table_name, $data);
        return $this;
    }
    
    /**
     * Sets up class with a PDO update query, also appends optional
     * update data if needed
     * 
     * @param String $table_name
     * @param Array $data
     * @return \DbPDO
     */
    public function update($table_name, $data = null) {
        $this->query = $this->fpdo->update($table_name);
        if (!empty($data)){
            $this->query = $this->query->set($data);
        }
        return $this;
    }
    
    /**
     * Sets up class with a PDO delete query
     * 
     * @param String $table_name
     * @return \DbPDO
     */
    public function delete($table_name){
        $this->query = $this->fpdo->deleteFrom($table_name);
        return $this;
    }
    
    /**
     * Magic method call so we can use reserved words in this class
     * such as "and"
     * 
     * @param String $func
     * @param Array $args
     * @return This
     */
    public function __call($func, $args){
        switch ($func){
            case 'and':
                return $this->where($args[0], $args[1]);
                break;
            default:
                trigger_error("Call to undefined method ".__CLASS__."::$func()", E_USER_ERROR);
                die ();
        }
    }
    
    // Returns the SQL query string
    public function getSql(){
        if ($this->query !== null){
            return $this->query->getQuery();
        }
        return null;
    }
    
    // Returns the last insert id
    // WARNING: If execute is not called before hand, you will receive the
    // PDO object
    public function last_insert_id(){
        if ($this->query !== null){
            // This might be too much, oh well it works
            if ($this->query instanceof InsertQuery)
                $this->execute();

            return $this->query;
        }
        return null;
    }
}
