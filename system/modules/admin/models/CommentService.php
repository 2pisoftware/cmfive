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
    
    /**
     * An easy way to save a comment against an object
     * @param <DbObject> $object
     * @param <String> $message
     */
    public function addComment($object, $message) {
        $comment = new Comment($this->w);
        $comment->obj_table = $object->getDbTableName();
        $comment->obj_id = $object->id;
        $comment->comment = strip_tags($message);
        $comment->insert();
    }
    
    public function renderComment($text) {
    	require_once 'creole/creole.php';
    	$creole = new creole();
    	$options = null;
    	return $creole->parse(strip_tags($text));
    }
    
}