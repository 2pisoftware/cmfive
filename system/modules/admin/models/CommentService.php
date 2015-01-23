<?php

class CommentService extends DbService {
    
    public function getCommentsForTable($table = null, $object_id = null){
        $where = array("is_deleted" => 0);
        if (!empty($table)){
            if (is_a($table, "DbObject")){
                // This way is probably better cause you dont hard code the table name in anywhere
                $where["obj_table"] = $table->getDbTableName();
            } else {
                $where["obj_table"] = $table;
            }
            if (!empty($object_id)){
                $where["obj_id"] = $object_id;
            }
            return $this->getObjects("Comment", $where);
        }
        return null;
    }
    
    public function getComment($id = null){
        if (!empty($id)){
            return $this->getObject("Comment", array("id" => intval($id)));
        }
        return null;
    }
    
    public function renderComment($text) {
    	require_once 'creole/creole.php';
    	$creole = new creole();
    	$options = null;
    	return $creole->parse($text);
    }

}