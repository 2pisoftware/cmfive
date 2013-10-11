<?php

////////////////////////////////////////////
//				TASK GROUPS				  //
////////////////////////////////////////////

function viewtaskgrouptypes_ALL(Web &$w) {
	TaskLib::task_navigation($w, "Manage Task Groups");
    $task_groups = $w->Task->getTaskGroups();
    if ($task_groups) {
    	usort($task_groups, array("TaskService","sortbyGroup"));
    }
    // prepare column headings for display
	$line = array(array("Title","Type", "Description", "Default Assignee", "Actions"));

	// if task group exists, display title, group type, description, default assignee and button for specific task group info
	if ($task_groups) {
		foreach ($task_groups as $group) {
			$line[] = array($group->title,
			$group->getTypeTitle(),
			$group->description,
			$group->getDefaultAssigneeName(),
			Html::b(WEBROOT."/task-group/viewmembergroup/".$group->id," More Info ")
			);
		}
	}
	else {
		// if no groups for this group type, say as much
		$line[] = array("There are no Task Groups Configured. Please create a New Task Group.","","","","");
	}
	
	// display list of task groups in the target task group type
	$w->ctx("dashboard",Html::table($line,null,"tablesorter",true));

	// tab: new task group
	// get generic task group permissions
	$arrassign = $w->Task->getTaskGroupPermissions();
	// unset 'ALL' given all can never assign a task
	unset($arrassign[0]);
	
	// set Is Task Active dropdown
	$is_active = array(array("Yes","0"), array("No","1"));

	$grouptypes = $w->Task->getAllTaskGroupTypes();
	
	// build form to create a new task group within the target group type
	$f = Html::form(array(
	array("Task Group Attributes","section"),
	array("Task Group","select","task_group_type",null,$grouptypes),
	array("Title","text","title"),
	array("Who Can Assign","select","can_assign",null,$arrassign),
	array("Who Can View","select","can_view",null,$w->Task->getTaskGroupPermissions()),
	array("Who Can Create","select","can_create",null,$w->Task->getTaskGroupPermissions()),
	array("Active","select","is_active",null,$is_active),
	array("","hidden","is_deleted","0"),
	array("Description","textarea","description",null,"26","6"),
	array("Default Assignee","select","default_assignee_id",null,$w->Auth->getUsers()),
	),$w->localUrl("/task-group/createtaskgroup"),"POST","Save");

	// display form
	$w->ctx("creategroup",$f);
}

// display an editable form showing attributes of a task group
function viewtaskgroup_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// return task group details given a task group ID
	$group_details = $w->Task->getTaskGroup($p['id']);

	// if is_active is set to '0', display 'Yes', else display 'No'
	$isactive = $group_details->is_active == "0" ? "Yes" : "No";

	// set Is Task Active, Is Task Deleted dropdowns for display
	$is_active = array(array("Yes","0"), array("No","1"));
	$is_deleted = array(array("Yes","1"), array("No","0"));
	
	// get generic task group permissions
	$arrassign = $w->Task->getTaskGroupPermissions();
	// unset 'ALL' given all can never assign a task
	unset($arrassign[0]);
	
	// build form displaying current attributes from database
	$f = Html::form(array(
	array("Task Group Details","section"),
	array("Task Group Type","static","task_group_type",$group_details->getTypeTitle()),
	array("Title","text", "title",$group_details->title),
	array("Who Can Assign","select", "can_assign",$group_details->can_assign,$arrassign),
	array("Who Can View","select", "can_view",$group_details->can_view,$w->Task->getTaskGroupPermissions()),
	array("Who Can Create","select","can_create",$group_details->can_create,$w->Task->getTaskGroupPermissions()),
	array("Is Active","select", "is_active", $group_details->is_active,$is_active),
	array("Description","textarea", "description",$group_details->description,"26","6"),
	array("Default Assignee","select", "default_assignee_id",$group_details->default_assignee_id,$w->Task->getMembersInGroup($p['id'])),
	),$w->localUrl("/task-group/updatetaskgroup/".$group_details->id),"POST"," Update ");

	// display form
	$w->setLayout(null);
	$w->ctx("viewgroup",$f);
}

function createtaskgroup_POST(Web &$w) {
	/*
	 $errors = $w->validate(array(
	 array("title","^$","Please enter a Title"),
	 array("can_assign","^$","Please select Who Can Assign"),
	 array("can_view","^$","Please select Who Can View"),
	 array("can_create","^$","Please select Who Can Create Tasks"),
	 array("is_active","^$","Please select if Task is Active"),
	 array("is_deleted","^$","Please select if Task is Deleted"),
	 array("description","^$","Please enter a Description"),
	 array("default_assignee_id","^$","Please enter an Assignee"),
	 array("task_group_type","^$","Please select a Task Type"),
	 ));

	 if (sizeof($errors) != 0) {
	 $w->error(implode("<br/>\n",$errors),"/task-group/creategroup");
	 }
	 */

	// insert newly created task group into the task_group database
	$taskgroup = new TaskGroup($w);
	$taskgroup->fill($_REQUEST);
	$taskgroup->insert();
	
	// if created succcessfully, create default notify matrix: all on
	if ($taskgroup->id) {
		$arr['guest']['creator'] = 1;
		$arr['member']['creator'] = 1;
		$arr['member']['assignee'] = 1;
		$arr['owner']['creator'] = 1;
		$arr['owner']['assignee'] = 1;
		$arr['owner']['other'] = 1;

		// so foreach role/type lets put the values in the database
		foreach ($arr as $role => $types) {
			foreach ($types as $type => $value) {
				$notify = new TaskGroupNotify($w);
				$notify->task_group_id = $taskgroup->id;
				$notify->role = $role;
				$notify->type = $type;
				$notify->value = $value;
				$notify->insert();
			}
		}
	}

	// if task group is successfully created and a default assignee is defined
	// create a task group membership list and set this person as the task group owner
	// if no default assignee, a task group membership list can be created at any time
	if (($taskgroup->id) && ($_REQUEST['default_assignee_id'] != "")) {
		$arrdb = array();
		$arrdb['task_group_id'] = $taskgroup->id;
		$arrdb['user_id'] = $_REQUEST['default_assignee_id'];
		$arrdb['role'] = "OWNER";
		$arrdb['priority'] = 1;
		$arrdb['is_active'] = 0;
	
		$mem = new TaskGroupMember($w);
		$mem->fill($arrdb);
		$mem->insert();
	}

	// return
	$w->msg("Task Group ".$taskgroup->title." added","/task-group/viewmembergroup/".$taskgroup->id);
}

function updatetaskgroup_POST(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of task group being edited
	$group_details = $w->Task->getTaskGroup($p['id']);

	// if group exists, update the details
	if ($group_details) {
		$group_details->fill($_REQUEST);
		$group_details->update();

		// if a default assignee is set, return their membership object for this group
		if ($_REQUEST['default_assignee_id'] != "") {
			$mem = $w->Task->getMemberGroupById($group_details->id, $_REQUEST['default_assignee_id']);
		
			// populate an array with the required details for updating
			// if the person is already a member we will maintain their current role
			// otherwise we will make them the group owner. we also make them active in case they had been previously removed from the group
			$arrdb = array();
			$arrdb['task_group_id'] = $group_details->id;
			$arrdb['user_id'] = $_REQUEST['default_assignee_id'];
			$arrdb['role'] = $mem->role ? $mem->role : "OWNER";
			$arrdb['priority'] = 1;
			$arrdb['is_active'] = 0;
			
			// if they don't exist, create the membership entry, otherwise update their current entry
			if (!$mem) {
				$mem = new TaskGroupMember($w);
				$mem->fill($arrdb);
				$mem->insert();
				}
			else {
				$mem->fill($arrdb);
				$mem->update();
				}
			}
		
		// return with message
		$w->msg("Task Group " . $group_details->title . " updated.","/task-group/viewmembergroup/".$group_details->id);
	}
	else {
		// if group somehow no longer exists, say as much
		$w->msg("Group: " . $_REQUEST['title'] . " no longer exists?","/task-group/viewtaskgroups/".$group_details->task_group_type);
	}
}

function deletetaskgroup_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of task group to be deleted
	$group_details = $w->Task->getTaskGroup($p['id']);

	// if is_active is set to '0', display 'Yes', else display 'No'
	$isactive = $group_details->is_active == "0" ? "Yes" : "No";

	// build static form displaying group details for confirmation of delete
	$f = Html::form(array(
	array("Task Group Details","section"),
	array("Task Group Type","static", "task_group_type",$group_details->getTypeTitle()),
	array("Title","static", "title",$group_details->title),
	array("Who Can Assign","static", "can_assign",$group_details->can_assign),
	array("Who Can View","static", "can_view",$group_details->can_view),
	array("Who Can Create","static", "can_create",$group_details->can_create),
	array("Is Active","static", "is_active", $isactive),
	array("Description","static", "description",$group_details->description),
	array("Default Assignee","static", "default_assignee_id",$w->Task->getUserById($group_details->default_assignee_id)),
	),$w->localUrl("/task-group/deletetaskgroup/".$group_details->id),"POST"," Delete ");

	// display form
	$w->setLayout(null);
	$w->ctx("viewgroup",$f);
}

function deletetaskgroup_POST(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of task group to be deleted
	$group_details = $w->Task->getTaskGroup($p['id']);

	// set 'is_deleted' flag to 1
	$_REQUEST['is_deleted'] = 1;

	// if gorup exists, update setting is_deleted to 1
	if ($group_details) {
		$group_details->fill($_REQUEST);
		$group_details->update();

		// return message
		$w->msg("Task Group " . $group_details->title . " deleted.","/task-group/viewtaskgroups/".$group_details->task_group_type);
	}
	else {
		// if group somehow no longer exists, say as much
		$w->msg("Group: " . $_REQUEST['title'] . " no longer exists?","/task-group/viewtaskgroups/".$group_details->task_group_type);
	}
}

////////////////////////////////////////////////////
//			MEMBER GROUPS						  //
////////////////////////////////////////////////////

// clicking the 'More Info' button for a task group gives all details specific to this group
// including group attributes and group membership
function viewmembergroup_GET(Web &$w) {
	$p = $w->pathMatch("id");
	
	// tab: Members
	// get all members in a task group given a task group ID
	$member_group = $w->Task->getMemberGroup($p['id']);

	// get the group attributes given a task group ID
	$group = $w->Task->getTaskGroup($p['id']);

	// put the group title into the page heading
	TaskLib::task_navigation($w, "Task Group - " . $group->title);
	
	// set columns headings for display of members
	$line[] = array("Member","Role","");

	// if their are members, display their full name, role and buttons to edit or delete the member
	if ($member_group) {
		foreach ($member_group as $member) {
			$line[] = array($w->Task->getUserById($member->user_id), $member->role,
				Html::box(WEBROOT."/task-group/viewmember/".$member->id," Edit ", true) .
				"&nbsp;&nbsp;" . 
				Html::box(WEBROOT."/task-group/deletegroupmember/".$member->id," Delete ", true)
				);
			}
		}
	else {
		// if there are no members, say as much
		$line[] = array("Group currently has no members. Please Add New Members.", "", "");
	}

	// enter task group attributes sa query string for buttons providing group specific functions such as delete or add members
	$w->ctx("taskgroup",$group->task_group_type);
	$w->ctx("grpid",$group->id);
	$w->ctx("groupid",$p['id']);

	// display list of group members 
	$w->ctx("viewmembers",Html::table($line,null,"tablesorter",true));
	
	// tab:  Notify
	$notify = $w->Task->getTaskGroupNotify($group->id);
	
	if ($notify) {
		foreach ($notify as $n) {
			$v[$n->role][$n->type] = $n->value;
		}
	}
	else {
		$v['guest']['creator'] = 0;
		$v['member']['creator'] = 0;
		$v['member']['assignee'] = 0;
		$v['owner']['creator'] = 0;
		$v['owner']['assignee'] = 0;
		$v['owner']['other'] = 0;
	}
	
	$notifyForm['Task Group Notifications'] = array(
	array(array("","hidden", "task_group_id",$group->id)),
	array(
		array("","static",""),
		array("Creator","static","creator"),
		array("Assignee","static","assignee"),
		array("All Others","static","others"),
		),
	array(
		array("Guest","static","guest"),
		array("","checkbox","guest_creator",$v['guest']['creator'])
		),
	array(
		array("Member","static","member"),
		array("","checkbox","member_creator",$v['member']['creator']),
		array("","checkbox","member_assignee",$v['member']['assignee']),
		),
	array(
		array("Owner","static","owner"),
		array("","checkbox","owner_creator",$v['owner']['creator']),
		array("","checkbox","owner_assignee",$v['owner']['assignee']),
		array("","checkbox","owner_other",$v['owner']['other']),
		),
	);

	$w->ctx("notifymatrix",Html::multiColForm($notifyForm,$w->localUrl("/task-group/updategroupnotify/"),"POST"," Submit "));
}

function updategroupnotify_POST(Web &$w) {
	// lets get some values knowing that only checked checkboxes return a value
	$arr['guest']['creator'] = $_REQUEST['guest_creator'] ? $_REQUEST['guest_creator'] : "0"; 
	$arr['member']['creator'] = $_REQUEST['member_creator'] ? $_REQUEST['member_creator'] : "0"; 
	$arr['member']['assignee'] = $_REQUEST['member_assignee'] ? $_REQUEST['member_assignee'] : "0"; 
	$arr['owner']['creator'] = $_REQUEST['owner_creator'] ? $_REQUEST['owner_creator'] : "0"; 
	$arr['owner']['assignee'] = $_REQUEST['owner_assignee'] ? $_REQUEST['owner_assignee'] : "0"; 
	$arr['owner']['other'] = $_REQUEST['owner_other'] ? $_REQUEST['owner_other'] : "0"; 

	// so foreach role/type lets put the values in the database
	foreach ($arr as $role => $types) {
		foreach ($types as $type => $value) {
			// is there a record for this taskgroup > role > type?
			$notify = $w->Task->getTaskGroupNotifyType($_REQUEST['task_group_id'],$role,$type);
			
			// if yes, update, if no, insert
			if ($notify) {
				$notify->value = $value;
				$notify->update();
				}
			else {
				$notify = new TaskGroupNotify($w);
				$notify->task_group_id = $_REQUEST['task_group_id'];
				$notify->role = $role;
				$notify->type = $type;
				$notify->value = $value;
				$notify->insert();
			}
		}
	}
	
	// return
	$w->msg("Notifications Updated","/task-group/viewmembergroup/".$_REQUEST['task_group_id']."/?tab=2");
}

function viewmember_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// get member details for edit
	$member = $w->Task->getMemberById($p['id']);

	// build editable form for a member allowing change of membership type
	$f = Html::form(array(
	array("Member Details","section"),
	array("Name","static", "name", $w->Task->getUserById($member->user_id)),
	array("Role","select","role",$member->role,$w->Task->getTaskGroupPermissions())
	),$w->localUrl("/task-group/updategroupmember/".$member->id),"POST"," Update ");

	// display form
    $w->setLayout(null);
	$w->ctx("viewmember",$f);
	}

function updategroupmember_POST(Web &$w) {
	$p = $w->pathMatch("id");
	$member = $w->Task->getMemberById($p['id']);
	$tgid = $member->task_group_id;

	$member->fill($_REQUEST);
	$member->update();

	$w->msg("Task Group updated","/task-group/viewmembergroup/".$tgid);
}

function addgroupmembers_GET(Web &$w) {
	$p = $w->pathMatch("task_group_id");

	// get all users
	$users = $w->Auth->getUsers();
	
	// not interested in users who are really groups
	foreach ($users as $user) {
		if ($user->is_group == "0")
			$usr[] = $user;
	}
	
	// build 'add members' form given task group ID, the list of group roles and the list of users.
	// if current members are added as if new, their membership will be updated, not recreated, with the selected role
	$addUserForm['Add Group Members'] = array(
	array(array("","hidden", "task_group_id",$p['task_group_id'])),
	array(array("As Role","select","role","",$w->Task->getTaskGroupPermissions())),
	array(array("Add Group Members","multiSelect","member",null,$usr)));

	$w->setLayout(null);
	$w->ctx("addmembers",Html::multiColForm($addUserForm,$w->localUrl("/task-group/updategroupmembers/"),"POST"," Submit "));
}

function updategroupmembers_POST(Web &$w) {
	// populate input array with preliminary membership details pertaining to target task group
	// these details will be the same for all new members to be added to the group
	$arrdb = array();
	$arrdb['task_group_id'] = $_REQUEST['task_group_id'];
	$arrdb['role'] = $_REQUEST['role'];
	$arrdb['priority'] = 1;
	$arrdb['is_active'] = 0;

	// for each selected member, complete population of input array
	foreach ($_REQUEST['member'] as $member) {
		$arrdb['user_id'] = $member;
		// check to see if member already exists in this group
		$mem = $w->Task->getMemberGroupById($arrdb['task_group_id'], $arrdb['user_id']);
		
		// if no membership, create it
		if (!$mem) {
			$mem = new TaskGroupMember($w);
			$mem->fill($arrdb);
			$mem->insert();
			}
		else {
			// if membership does exists, update the record - only the role will be updated
			$mem->fill($arrdb);
			$mem->update();
		}
		// prepare input array for next selected member to insert/update
		unset($arrdb['user_id']);
	}
	// return
	$w->msg("Task Group updated","/task-group/viewmembergroup/".$_REQUEST['task_group_id']);
}

function deletegroupmember_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of member to be deleted
	$member = $w->Task->getMemberById($p['id']);
	
	// build a static form displaying members details for confirmation of delete
	$f = Html::form(array(
	array("Member Details","section"),
	array("","hidden", "is_active","1"),
	array("Name","static", "name", $w->Task->getUserById($member->user_id)),
	array("Role","static","role",$member->role)
	),$w->localUrl("/task-group/deletegroupmember/".$member->id),"POST"," Delete ");

	// display form
    $w->setLayout(null);
	$w->ctx("deletegroupmember",$f);
	}

function deletegroupmember_POST(Web &$w) {
	$p = $w->pathMatch("id");
	// get the details of the person to delete 
	$member = $w->Task->getMemberById($p['id']);
	// get the task group ID for returning to group display
	$tgid = $member->task_group_id;

	// if member exists, delete them
	if ($member) {
		// set is_active = 1
		$member->fill($_REQUEST);
		$member->update();

		// get group details, if person being deleted is the task group default assignee
		// set default_assigne_id = 0, ie noone. owners can edit task group as assign default assignee at any time
		$group = $w->Task->getTaskGroup($tgid);
		if ($member->user_id == $group->default_assignee_id) {
			$group->default_assignee_id = 0;
			$group->update();
		}
		// return
		$w->msg("Task Group updated","/task-group/viewmembergroup/".$tgid);
	}
	else {
		// if member somehow no longer exists, say as much
		$w->msg("Task Group Members no longer exists?","/task-group/viewmembergroup/".$tgid);
	}
}

?>