<?php
// Create Task: selecting Task Type dynamically loads the related task types, proprity and assignee's

function taskAjaxSelectbyTaskGroup_ALL(Web $w) {
	$tid = $w->request('id');
	$t = $w->Task->getTaskGroup($tid);

        if (empty($t->id)) {
            return;
        }
        
	$tasktypes = ($t != "") ? $w->Task->getTaskTypes($t->task_group_type) : array();
	$priority = ($t != "") ? $w->Task->getTaskPriority($t->task_group_type) : array();
	$members = ($t != "") ? $w->Task->getMembersBeAssigned($t->id) : array();
	sort($members);
	$typetitle = ($t != "") ? $t->getTypeTitle() : "";
	$typedesc = ($t != "") ? $t->getTypeDescription() : "";

	// if user cannot assign tasks in this group, leave 'first_assignee' blank for owner/member to delegate
	$members = ($t->getCanIAssign()) ? $members : array(array("Default",""));

	// create dropdowns loaded with respective data
	$ttype = Html::select("task_type",$tasktypes,null);
	$prior = Html::select("priority",$priority,null);
	$mem = Html::select("first_assignee_id",$members,null);
	$tasktext = "<table style='width: 100%;'>" .
            "<tr><td class=section colspan=2>Task Group Description</td></tr>" . 
            "<tr><td><b>Task Group</td><td>" . $t->title . "</td></tr>" . 
            "<tr><td><b>Task Type</b></td><td>" . $typetitle . "</td></tr>" . 
            "<tr valign=top><td><b>Description</b></td><td>" . $typedesc . "</td></tr>" . 
        "</table>";

	// return as array of arrays
	$result = array($ttype, $prior , $mem, $tasktext);

	$w->setLayout(null);
	$w->out(json_encode($result));
}
