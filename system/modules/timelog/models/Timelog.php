<?php
class Timelog extends DbObject {
    public $object_class;
    public $object_id;
    
    public $user_id;
    public $dt_start;
    public $dt_end;
    public $time_type;   
    public $is_suspect;
    
    public $creator_id;
    public $modifier_id;
    public $dt_created;
    public $dt_modified;
    public $is_deleted;


    public function getDuration() {
        if (!empty($this->dt_start) and !empty($this->dt_end)) {
            return ($this->dt_end - $this->dt_start);
        }
    }
    
    // Only return the first comment (comments are 1 - many association but we want to emulate 1 - 1)
    public function getComment() {
        $comments = $this->w->Comment->getCommentsForTable($this, $this->id);
        return !empty($comments[0]->id) ? $comments[0] : new Comment($this->w);
    }

    public function getLinkedObject() {
        if (!empty($this->object_class) && !empty($this->object_id)) {
            if (class_exists($this->object_class)) {
                return $this->getObject($this->object_class, $this->object_id);
            }
        }
    }
    
    public function start($object) {
        if (empty($object->id)) {
            return false;
        }
        
        $this->object_class = get_class($object);
        $this->object_id = $object->id;

        $this->dt_start = time();
        $this->user_id = $this->w->Auth->user()->id;
        $this->insert();
        
        return true;
    }
    
    public function stop() {
        $this->w->Log->debug("time " . $this->dt_end);
        if (empty($this->dt_end)) {
            $this->w->Log->debug("stopping time");
            $this->dt_end = time();
            $this->update();
        }
    }
    
}
