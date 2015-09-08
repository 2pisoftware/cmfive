<?php

/**
 * This service class aids in the registration and usage of timelog objects
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class TimelogService extends DbService {
    private $_trackObject = null;
    
	/**
	 * Returns all time logs for a given user
	 * 
	 * @param User $user
	 * @param boolean $includeDeleted
	 * @return Timelog
	 */
    public function getTimelogsForUser(User $user = null, $includeDeleted = false, $page = 1, $page_size = 20) {
        if ($user === null) {
            $user = $this->w->Auth->user();
        }
        
        $where = ['user_id' => $user->id];
        if (!$includeDeleted) {
            $where['is_deleted'] = 0;
        }
        
        return $this->getObjects("Timelog", $where, false, true, "dt_start DESC", ($page - 1) * $page_size, $page_size);
    }
	
	public function countTotalTimelogsForUser(User $user = null, $includeDeleted = false) {
        if ($user === null) {
            $user = $this->w->Auth->user();
        }
        
        $where = ['user_id' => $user->id];
        if (!$includeDeleted) {
            $where['is_deleted'] = 0;
        }
        
        return $this->db->get("timelog")->where($where)->count();
    }
	
	/**
	 * Returns all non deleted timelogs
	 * 
	 * @return Array<Timelog>
	 */
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
		$timelog = $this->getActiveTimeLogForUser();
        return !empty($timelog);
    }
    
    public function hasTrackingObject() {
        return !empty($this->_trackObject);
    }
    
    public function registerTrackingObject($object) {
        $this->_trackObject = $object;
    }
    
    public function getTrackingObject() {
        return $this->_trackObject;
    }
    
	public function getTrackingObjectClass() {
		if ($this->hasTrackingObject()) {
			return get_class($this->_trackObject);
		}
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

    public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? : array();

		$trackingObject = $w->Timelog->getTrackingObject();
		
        if ($w->Auth->loggedIn()) {
            $w->menuBox("timelog/edit" . (!empty($trackingObject) && !empty($trackingObject->id) ? "?class=" . get_class($trackingObject) . "&id=" . $trackingObject->id : ''), "Add Timelog", $nav);
        }

        $w->ctx("navigation", $nav);
        return $nav;
    }
}