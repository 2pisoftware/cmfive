<?php

class TimelogService extends DbService {
    private $_trackObject = null;
    
    public function getTimelogsForUser(User $user = null, $includeDeleted = false) {
        if ($user === null) {
            $user = $this->w->Auth->user();
        }
        
        $where = ['user_id' => $user->id];
        if (!$includeDeleted) {
            $where['is_deleted'] = 0;
        }
        
        return $this->getObjects("Timelog", $where);
    }
    
    public function getTimelogs() {
        return $this->getObjects("Timelog", ["is_deleted" => 0]);
    }
    
    public function getTimelog($id) {
        return $this->getObject("Timelog", $id);
    }
    
    public function getActiveTimeLogForUser() {
        return $this->getObject("Timelog", ["is_deleted" => 0, "dt_end" => null, "user_id" => $this->w->Auth->user()->id]);
    }
    
    public function hasActiveLog() {
        return !empty($this->getActiveTimeLogForUser());
    }
    
    public function hasTrackingObject() {
        return !empty($this->getTrackingObject());
    }
    
    public function registerTrackingObject($object) {
        $this->_trackObject = $object;
    }
    
    public function getTrackingObject() {
        return $this->_trackObject;
    }
    
    public function getJSTrackingObject() {
        if ($this->hasTrackingObject()) {
            $class = new stdClass();
            $class->class = get_class($this->_trackObject);
            $class->id = $this->_trackObject->id;
            return json_encode($class);
        }
    }

    public function shouldShowTimer() {
        // Check if tracking object set or existing timelog is running
        return ($this->hasTrackingObject() || $this->hasActiveLog());
    }

}