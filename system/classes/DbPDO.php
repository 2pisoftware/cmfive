<?php
/**
 * PDO Extension class, methods used are to emulate methods called from Crystal DB.
 * See: http://www.php.net/manual/en/book.pdo.php for the PDO Class reference
 * 
 * @author Adam Buckley for TripleACS
 */
class DbPDO extends PDO {
    private $statement = null;
    private $table_names = array();
    private $bind_parameters = array();
    
    public function __construct($config = array()) {
        $url = "{$config['driver']}:host={$config['hostname']};dbname={$config['database']}";
        parent::__construct($url,$config["username"],$config["password"], null);
        
        // Since you cant bind table names, maybe its a good idea to
        // load an array of table names to check against? But this is probably
        // unecessary to do on every call so maybe move it to get()
        foreach($this->query("show tables")->fetchAll(PDO::FETCH_NUM) as $table)
            $this->table_names[] = $table[0];

    } 

    public function get($table_name){
        if (!in_array($table_name, $this->table_names)){
            trigger_error("Table does not exist in the databse", E_USER_ERROR);
            return null;
        }     
        $this->statement = "SELECT * FROM " . $table_name . " ";// $this->prepare("SELECT * FROM " . $table_name);
        return $this;
    }
    
    public function where($column, $equals){
        if ($this->statement !== null){
            if (!strpos($this->statement, "WHERE"))
                $this->statement .= " WHERE ";
            else
                $this->statement .= " AND ";
            $this->statement .= " $column = :$column ";
            $this->bind_parameters[':'.$column] = $equals;
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
        $this->statement = $this->prepare($query);
        return $this;
    }
    
    /**
     * Executes a prepared statement
     * 
     * @return Result
     */
    public function execute(){
        if ($this->statement !== null){
            return $this->statement->execute();
        }
        return null;
    }
    
    /**
     * A grace method for our migration from Crystal
     * Crystal used "fetch_row" whereas PDO uses "fetch"
     * 
     * @return Result se
     */
    public function fetch_row() {
        if ($this->statement !== null){
            // Prepare statement if $this->statement is a string
            if (is_string($this->statement)){
                $this->statement = $this->prepare($this->statement);
            }
            // Bind parameters if not empty
            if (!empty($this->bind_parameters)){
                foreach($this->bind_parameters as $param => &$value){
                    $this->statement->bindValue($param, $value);
                }
            }

            return $this->statement->fetch();
        }
        return null;
    }
    
    /**
     * A grace method for our migration from Crystal
     * Crystal used "fetch_all" whereas PDO uses "fetchAll"
     * 
     * @return Result set
     */
    public function fetch_all(){
        if ($this->statement !== null){
            return $this->statement->fetchAll();
        }
        return null;
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
}
