<?php
class TaskService extends DbService {
	public $_tasks_loaded;
	
	// function to sort lists by date created
	static function sortByCreated($a, $b) {
    	if ($a->dt_created == $b->dt_created) {
			return 0;
		}
		return ($a->dt_created < $b->dt_created) ? +1 : -1;
	}

	// function to sort task time log by date started
	static function sortByStarted($a, $b) {
		if ($a->dt_start == $b->dt_start) {
			return 0;
		}
		return ($a->dt_start < $b->dt_start) ? +1 : -1;
	}

	// function to sort task group list by task type
	static function sortbyGroup($a, $b) {
    	if (strcasecmp($a->task_group_type, $b->task_group_type) == 0) {
			return 0;
		}
		return (strcasecmp($a->task_group_type, $b->task_group_type) > 0) ? +1 : -1;
	}
	
	// function to sort task lists by due date
	static function sortTasksbyDue($a, $b) {
    	if ($a->dt_due == $b->dt_due) {
			return 0;
		}
		return ($a->dt_due > $b->dt_due) ? +1 : -1;
	}

	// function to sort groups lists by users role
	static function sortbyRole($a, $b) {
    	if ($a->role == $b->role) {
			return 0;
		}
		return ($a->role > $b->role) ? +1 : -1;
	}
	
	// convert dd/mm/yyyy date to yyy-mm-dd for SQL statements
	function & date2db($date) {
		if ($date) {
			list($d,$m,$y) = preg_split("/\/|-|\./", $date);
			return $y."-".$m."-".$d;
		}
	}
	
	// nicely format a number of seconds as H:m
	function & getFormatPeriod($seconds) {
		if (is_numeric($seconds)) {
			$hours = intval($seconds/3600);
			$mins = intval(($seconds/60) % 60);
			$mins = str_pad($mins,2,"0",STR_PAD_LEFT);
			return $hours.":".$mins;
		}
	}

	// return a date one week behind 'today'
	function getLastWeek() {
		$cd = strtotime(date("Y-m-d"));
		$newdate = date("d/m/Y", mktime(0, 0, 0, date("m",$cd), date("d",$cd)-7, date("Y",$cd)));
		return $newdate;
	}
	
	// return a date one month in advance of 'today'
	function getNextMonth() {
		$cd = strtotime(date("Y-m-d"));
		$newdate = date("d/m/Y", mktime(0, 0, 0, date("m",$cd)+1, date("d",$cd), date("Y",$cd)));
		return $newdate;
	}
	
	// mark up URLS as <a> links
	function & findURL($text) {
		if ($text != "") {
			// decode text back to HTML entities
			$text = htmlspecialchars_decode($text);
			// find URLs - may be more than one
			preg_match_all("/https?:\/\/[a-zA-Z0-9\.\/\?&=\-\%_\+]*/",$text, $urls);
			
			if ($urls) {
				foreach ($urls as $url) {
					foreach ($url as $u) {
						// foreach URL create a marker to replace the URL.
						// create array, key: marker + value: marked-up URL.
						// use RAND to improve uniqueness of marker in the text so no accidental string substitutions
						$marker = "URLMARKER" . rand(100,999);
						$newurl = "<a href=\"" . $u . "\" target=\"_blank\">" . $u . "</a>";
						$mark[$marker] = $newurl;
						$text = str_replace($u,$marker,$text);
					}
				}
				// again encode the text
				$text = htmlspecialchars($text);
				
				// replace the markers in the encoded text with unencoded URLs
				if ($mark) {
					foreach ($mark as $marker => $url) {
						$text = str_replace($marker,$url,$text);
					}
				}
			}
		}
		// return the text
		return str_replace("\r\n","<br>",$text);
	}

	// get a task group from the database by its ID
    function & getTaskGroup($id) {
		return $this->getObject("TaskGroup",$id);
	}
		
	// get all active task groups from the database
	function & getTaskGroups() {
		return  $this->getObjects("TaskGroup", array("is_active"=>0,"is_deleted"=>0));
	}

	// get all task groups from the database of given task group type
	function & getTaskGroupsByType($id) {
		return $this->getObjects("TaskGroup",array("is_active"=>0,"is_deleted"=>0,"task_group_type"=>$id));
	}

	// get all task group types as defined in our tasks file
	function & getAllTaskGroupTypes() {
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
        	if (startsWith($class, "TaskGroupType_")) {
        		$tgt = new $class($this->w);
        		$taskgrouptypes[] = array($tgt->getTaskGroupTypeTitle(), $class);
        	}
        }
        return $taskgrouptypes;
	}
    
	// prepare to get all task groups of type $class as defined in our tasks file
	function & getTaskGroupTypeObject($class) {
		return $this->_getTaskObjectGeneric($class,"TaskGroupType_");
	}

	// prepare to get all task types of type $class as defined in our tasks file
	function & getTaskTypeObject($class) {
        return $this->_getTaskObjectGeneric($class,"TaskType_");
	}
	
	// get all task groups or task types of type $class as defined in our task file
	function & _getTaskObjectGeneric($class,$type) {
        $this->_loadTaskFiles();
        $class = startsWith($class, $type) ? $class : $type.$class;
		if (class_exists($class)) {
			return new $class($this->w);
		}
		return null;
	}
	
	// return the task group type by a task group ID
	function & getTaskGroupTypeById($id) {
		$c = $this->getTaskGroup($id);
		return $c->task_group_type;
	}	

	// return the task group type by a task group ID
	function & getTaskGroupTitleById($id) {
		$c = $this->getTaskGroup($id);
		return $c->title;
	}	
	
	// return the task group description as defined in our tasks file for a given type/class
	function & getTaskGroupDescription($class) {
        $this->_loadTaskFiles();
   		$tgt = new $class($this->w);
   		return $tgt->getTaskGroupTypeDescription();
	}

	// return the task group flag, re: can tasks be reopened as defined in our tasks file for a given type/class
	function & getCanTaskReopen($taskgroup) {
        $this->_loadTaskFiles();
        if (class_exists($taskgroup)) {
        	$c = new $taskgroup($this->w);
       		return $c->getCanTaskGroupReopen();
        }
        return false;
	}
	
	// return user notify record given task ID, user id
	function & getTaskUserNotify($id,$tid) {
		return $this->getObject("TaskUserNotify",array("user_id"=>$id,"task_id"=>$tid));	
	}
	
	// return all notify records given user id and taskgroup ID
	function & getTaskGroupUserNotify($id,$tid) {
		return $this->getObjects("TaskGroupUserNotify",array("user_id"=>$id,"task_group_id"=>$tid));	
	}
	
	// return notify record for user given user id, taskgroup ID, role and type
	function & getTaskGroupUserNotifyType($id,$tid,$role,$type) {
		return $this->getObject("TaskGroupUserNotify",array("user_id"=>$id,"task_group_id"=>$tid,"role"=>$role,"type"=>$type));	
	}
	
	// return the recordset of notify matrix for given Task Group
	function getTaskGroupNotify($id) {
		return $this->getObjects("TaskGroupNotify",array("task_group_id"=>$id));
	}
	
	// return notify record for Task Group given taskgroup ID, role and type
	function & getTaskGroupNotifyType($id,$role,$type) {
		return $this->getObject("TaskGroupNotify",array("task_group_id"=>$id,"role"=>$role,"type"=>$type));	
	}

	// static list of group permissions for can_view, can_assign, can_create
	function & getTaskGroupPermissions() {
    	return array("ALL","GUEST","MEMBER","OWNER");
    }

    // determine if current user can perform a task
    // compare users role against required role to perform given task
    function & getMyPerms($role,$permission) {
    	$permissions = $this->getTaskGroupPermissions();

    	// key = permission level, value = ascending number
    	foreach ($permissions as $per) {
    		$perm[$per] = $i++;
    	}

    	// if number of user role is >= number of requesite level, then allow
    	if ($perm[$role] >= $perm[$permission]) {
    		return true;
    	}
    	return false;
    }

    // given a where clause, return all resulting tasks from the database
	function & getTasks($id=null, $where=null) {
		$assign = "";
		$grps = "";

		// if no user ID given, show all tasks in groups of which logged in user is a member
		// get list of groups for inclusion in where clause
		$groups = $this->getMemberGroups($_SESSION['user_id']);
		if ($groups) {
			foreach ($groups as $group) {
				$grplist .= $group->task_group_id . ",";
			}
			$grplist = rtrim($grplist,",");
			$grps = "t.task_group_id in (" . $grplist . ") and ";
		}
			
		// if where is array, do this
		if (is_array($where)) {
			if (($id) && ($id = ""))
				 	$where['t.assignee_id'] = $id;
			$where['g.is_active'] = 0;
			$where['g.is_deleted'] = 0;
			$where['t.is_deleted'] = 0;
		}
		// if where is not blank string, do this
		elseif ($where != "") {
			if (($id) && ($id != "")) {
				$assign = "(t.assignee_id = " . $id . " or t.assignee_id = 0) and ";
			}
			$where = "where " . $assign . $grps . $where . " and t.is_deleted = 0 and g.is_active = 0 and g.is_deleted = 0";
		}
		// if where is blank string, do this
		elseif ($where == "") {
			if (($id) && ($id != "")) {
				$assign = "(t.assignee_id = " . $id . " or t.assignee_id = 0) and ";
			}
			$where = "where " . $assign . $grps . " t.is_closed = 0 and t.is_deleted = 0 and g.is_active = 0 and g.is_deleted = 0";
		}
		
//		return $this->getObjects("Task",$clause);
		// need to check if task group is deleted
		$rows = $this->_db->sql("SELECT t.* from ".Task::getDbTableName()." as t inner join ".TaskGroup::getDbTableName()." as g on t.task_group_id = g.id " . $where . " order by t.task_group_id")->fetch_all();
		$rows = $this->fillObjects("Task",$rows);
		return $rows;
	}

	// return a task group from the database given its ID
	function & getTasksbyGroupId($id) {
		$where = ($id) ? array("task_group_id"=>$id) : null;
		return $this->getObjects("Task",$where);
	}
	
	// given a where clause, return all tasks created by a given user ID
	// required to join with modifiable aspect to determine task creator
	function & getCreatorTasks($id, $clause=null) {
		if (is_array($clause)) {
			foreach ($clause as $name => $value) {
				$where .= "and t." . $name . " = '" . $value . "' ";
			}
		}
		elseif ($clause != "") {
			$where = " and " . $clause;
		}
		$where .= " and t.is_deleted = 0 and g.is_active = 0 and g.is_deleted = 0";

		// check that task group is active and not deleted
		$rows = $this->_db->sql("SELECT t.* from ".Task::getDbTableName()." as t inner join ".ObjectModification::getDbTableName()." as o on t.id = o.object_id inner join ".TaskGroup::getDBTableName()." as g on t.task_group_id = g.id where o.creator_id = " . $id . " and o.table_name = '".Task::getDbTableName()."' " . $where . " order by t.id")->fetch_all();
		$rows = $this->fillObjects("Task",$rows);
		return $rows;
	}

    // return all resulting tasks from the database modified in the last week
	function & getTaskWeek($group, $assignee, $from, $to) {
		$grps = $who = "";

		// if no group supplied, get all my groups
		if ($group != "") {
			$grplist = $group;
		}
		else {
			// list the groups i am a member of
			$groups = $this->getMemberGroups($_SESSION['user_id']);
				if ($groups) {
					foreach ($groups as $group) {
						$grplist .= $group->task_group_id . ",";
					}
				} else {
					return null;
				}
			$grplist = rtrim($grplist,",");
		}

		if ($assignee != "")
			$who = " c.creator_id = " . $assignee . " and ";
					
		// create where clause giving all active tasks which have shown activity in the last week
		// need to check if task group is deleted
		$grps = "t.task_group_id in (" . $grplist . ") and ";
		$where = "where " . $grps . $who . " t.is_deleted = 0 and g.is_active = 0 and g.is_deleted = 0";
		$where .= " and date_format(c.dt_modified,'%Y-%m-%d') >= '" . $this->date2db($from) . "' and date_format(c.dt_modified,'%Y-%m-%d') <= '" . $this->date2db($to) . "'";

		// get and return tasks
		$rows = $this->_db->sql("SELECT t.id, t.title, t.task_group_id, c.comment, c.creator_id, c.dt_modified from ".Task::getDbTableName()." as t inner join ".TaskComment::getDbTableName()." as c on t.id = c.obj_id and c.obj_table = '".Task::getDbTableName()."' inner join ".TaskGroup::getDBTableName()." as g on t.task_group_id = g.id " . $where . " order by c.dt_modified desc")->fetch_all();
		return $rows;
	}
	
	// get a task from the database given its ID
	function & getTask($id) {
		return $this->getObject("Task",$id);
	}

	// get the task data from the database given a task ID
	function & getTaskData($id) {
		return $this->getObjects("TaskData",array("task_id"=>$id));
	}

	// return an array for display of task type for a task group defined in our tasks file.
	function & getTaskTypes($taskgroup) {
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
        	if (startsWith($class, $taskgroup)) {
        		$tgt = new $class($this->w);
        		foreach ($tgt->getTaskTypeArray() as $short_tasktype => $long_tasktype) {
	        		$tasktypes[] = array($long_tasktype, $short_tasktype);
        		}
        	}
        }
        return $tasktypes;
	}

	// returns an array of statuses of a task group defined in our tasks file
	function & getTaskStatus($taskgroup) {
        $this->_loadTaskFiles();
        if (class_exists($taskgroup)) {
        	$c = new $taskgroup($this->w);
        	if (is_a($c, "TaskGroupType")) {
        		return $c->getStatusArray();
        	}
        }
	}
	
	// returns an array for display of statuses of a task group defined in our tasks file
	function & getTaskTypeStatus($taskgroup) {
        $this->_loadTaskFiles();
   		$arrstatus = $this->getTaskStatus($taskgroup);
   		if ($arrstatus) {
			foreach ($arrstatus as $status) {
				$statuses[] = array($status[0], $status[0]);
   				}
        	return $statuses;
   		}
	}
	
	// returns an array for display of priorities of a task group defined in our tasks file
	function & getTaskPriority($taskgroup) {
        $this->_loadTaskFiles();
       	if (class_exists($taskgroup)) {
        	$tgt = new $taskgroup($this->w);
        	if (is_a($tgt, "TaskGroupType")) {
        		$priority = $tgt->getTaskPriorityArray();
        		foreach ($priority as $taskpriority) {
	        		$taskprior[] = array($taskpriority, $taskpriority);
        		}
        	}
       	}
        return $taskprior;
	}

	// returns the additional form fields for a task type as defined in our task file
	function & getFormFieldsByTask($tasktype,TaskGroup $tg) {
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
        	if (startsWith($class, "TaskType_".$tasktype)) {
        		$tgt = new $class($this->w);
        		$fieldform = $tgt->getFieldFormArray($tg);
        	}
        }
        return $fieldform;
	}
    
	// return a task comment by the COMMENT ID
	function & getComment($id) {
		return $this->w->Auth->getObject("TaskComment",array("obj_table"=>Task::getDbTableName(),"id"=>$id));
	}
	
	// return a time log entry by log entry ID
	function & getTimeLogEntry($id) {
		return $this->getObject("TaskTime",array("id"=>$id,"is_deleted"=>0));
	}
	
	// return an array of the owners of a task group from the database
	function & getTaskGroupOwners($id) {
		return $this->getObjects("TaskGroupMember",array("task_group_id"=>$id,"role"=>"OWNER","is_active"=>0));
	}
	
	// determine if a given user is an owner of a task group.
	// input: task group ID & user ID
	function & getIsOwner($task_group_id, $user_id) {
		$owners = $this->getTaskGroupOwners($task_group_id);
		if ($owners) {
			foreach ($owners as $owner) {
				if ($owner->user_id == $user_id)
					return true;
			}
		}
		return false;
	}
	
	// return all groups from the database of which a user is a member, given user ID. else, return all groups
	function & getMemberGroups($id=null) {
//		$where = ($id) ? array("user_id"=>$id) : null;
//		return $this->getObjects("TaskGroupMember",$where);

		// check if task group is active and not deleted
		$where = "where m.user_id = " . $id . " and m.is_active = 0 and g.is_active = 0 and g.is_deleted = 0";
		$rows = $this->_db->sql("SELECT m.* from ".TaskGroupMember::getDbTableName()." as m inner join ".TaskGroup::getDbTableName()." as g on m.task_group_id = g.id " . $where . " order by m.task_group_id")->fetch_all();
		$rows = $this->fillObjects("TaskGroupMember",$rows);
		return $rows;
	}

	// return all members of a task group from the database, given the task group ID
	function & getMemberGroup($id) {
		return $this->getObjects("TaskGroupMember",array("task_group_id"=>$id,"is_active"=>0));
	}

	// return an array for display of all members in a given task group, by task group ID
	function & getMembersInGroup($id) {
		$members = $this->getObjects("TaskGroupMember",array("task_group_id"=>$id,"is_active"=>0));
		foreach ($members as $member) {
			$line[] = array($this->getUserById($member->user_id),$member->user_id);
		}
		return $line;
	}
	
	// return an array for display of all members of a task group who can be assigned tasks, given task group ID
	function & getMembersBeAssigned($id) {
		$where = "task_group_id = " . $id . " and (role = 'MEMBER' or role = 'OWNER') and is_active = 0";
		$members = $this->getObjects("TaskGroupMember",$where);
		foreach ($members as $member) {
			$line[] = array($this->getUserById($member->user_id),$member->user_id);
		}
		return $line;
	}

	// return a member object given the task_group_member database ID: targets specific member in specific task group
	function & getMemberById($id) {
		return $this->getObject("TaskGroupMember",array("id"=>$id));
	}

	// return a member object given a task group ID and a user ID
	function & getMemberGroupById($group, $uid) {
		return $this->getObject("TaskGroupMember",array("task_group_id"=>$group,"user_id"=>$uid,"is_active"=>0));
	}

	// return a users full name given their user ID
	function & getUserById($id) {
		$u = $this->w->Auth->getUser($id);
		return $u ? $u->getFullName() : "";
	}

	// load our task files to make available: titles, descriptions, status, additional form fields, etc.
	// for defined task groups amd task types
	function _loadTaskFiles() {
        // do this only once
        if ($this->_tasks_loaded)
            return;

        $handlers = $this->w->modules();
        foreach ($handlers as $model) {
            $file = $this->w->getModuleDir($model).$model.".tasks.php";
            if (file_exists($file)) {
                require_once $file;
            }
        }
        $this->_tasks_loaded = true;
    }
}
