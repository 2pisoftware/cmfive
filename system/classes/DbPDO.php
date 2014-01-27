<?php
/**
 * PDO Extension class, methods used are to emulate methods called from Crystal DB.
 * See: http://www.php.net/manual/en/book.pdo.php for the PDO Class reference
 * 
 * @author Adam Buckley for TripleACS
 */
class DbPDO extends PDO {
    private static $table_names = array();
    private static $_QUERY_CLASSNAME = "PDOStatement";

    private $query = null;
    private $fpdo = null;
    public $sql = null;
    public $total_results = 0;
    
    public function __construct($config = array()) {
        // Set up our PDO class
        $port = isset($config['port']) && !empty($config['port']) ? ";port=".$config['port'] : "";
        $url = "{$config['driver']}:host={$config['hostname']};dbname={$config['database']}{$port}";
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
        return $this;
    }
    
    public function select($select){
        if ($this->query !== NULL && !empty($select)){
            $this->query = $this->query->select($select);
        }
        return $this;
    }
    
    public function count(){
        if ($this->query !== null){
            $result = $this->select("count(*)")->fetch_element("count(*)");
            return intval($result);
        }
    }
    
    public function leftJoin($leftJoin){
        if ($this->query !== NULL && !empty($leftJoin)){
            $this->query = $this->query->leftJoin($leftJoin);
        }
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
        }
        return $this;
    }
    
    public function order_by($orderby){
        if ($this->query !== null && !empty($orderby)){
            $this->query = $this->query->orderBy($orderby);
        }
        return $this;
    }
    
    public function limit($limit){
        if ($this->query !== null and !is_null($limit)){
            $this->query = $this->query->limit($limit);
        }
        return $this;
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
        $this->query = $this->query($query);
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
    
    public function fetch_element($element){
        $row = $this->fetch_row();
        return (!is_null($row[$element]) ? $row[$element] : null);
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
        if (!empty($this->query)){
            return $this->query->fetchAll();
        }
        return array();
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
        if ($func[0] == "_"){
            $func = substr($func, 1);
        }
        switch ($func){
            case 'and':
                return $this->where($args[0], $args[1]);
                break;
            
            default:
                // What this does is palm off unknown function calls to the parent
                // which will still throw an error if the method doesnt exist BUT
                // with the code above that strips off the leading underscore if present will mean
                // that we can bypass the whacky adapted DbPDO/FluentPDO and go STRAIGHT to the
                // underlying PDO implementation, just by prefixing underscores to the first method call! Amazing!
                
                // NOTE: You only need to prefix the first method when chaining as the return value for
                // the first call is a PDOStatement
                
                return call_user_func_array("parent::".$func, $args);
        }
    }
    
    // Returns the SQL query string
    public function getSql(){
        if ($this->query !== null){
            return $this->query->getQuery();
        }
        return null;
    }
    
    public function columnCount() {
        return $this->query->columnCount();
    }
    
    public function getColumnMeta($i) {
        return $this->query->getColumnMeta($i);
    }

    public function clear_sql(){
        // Clear everything
        if (!empty($this->query) and is_a($this->query, DbPDO::$_QUERY_CLASSNAME)) {
            $this->query = $this->query->where(null);
            $this->query = $this->query->orderBy(null);
            $this->query = $this->query->limit(null);
            $this->query = $this->query->offset(null);
            $this->query = $this->query->fetch(null);
            $this->query = $this->query->select(null);
        }
        return $this;
    }

    // Returns the last insert id
    // WARNING: If execute is not called before hand, you will receive the
    // PDO object
    public function last_insert_id(){
        if ($this->query !== null){
            // This might be too much, oh well it works
            // It checks if we havent called execute yet, and calls it for us
            if ($this->query instanceof InsertQuery)
                $this->execute();

            return $this->query;
        }
        return null;
    }
}
