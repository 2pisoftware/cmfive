<?php
// Create Task: selecting Task Type dynamically loads the related task types, proprity and assignee's

function taskAjaxSelectbyTaskGroup_ALL(Web $w) {
    $p = $w->pathMatch("taskgroup_id");
    $taskgroup = $w->Task->getTaskGroup($p['taskgroup_id']);

    if (empty($taskgroup->id)) {
        return;
    }

    $tasktypes = ($taskgroup != "") ? $w->Task->getTaskTypes($taskgroup->task_group_type) : array();
    $priority = ($taskgroup != "") ? $w->Task->getTaskPriority($taskgroup->task_group_type) : array();
    $members = ($taskgroup != "") ? $w->Task->getMembersBeAssigned($taskgroup->id) : array();
    sort($members);
    $typetitle = ($taskgroup != "") ? $taskgroup->getTypeTitle() : "";
    $typedesc = ($taskgroup != "") ? $taskgroup->getTypeDescription() : "";

    // if user cannot assign tasks in this group, leave 'first_assignee' blank for owner/member to delegate
    $members = ($taskgroup->getCanIAssign()) ? $members : array(array("Default",""));

    // create dropdowns loaded with respective data
    //$ttype = Html::select("task_type",$tasktypes,null);
    $ttype = Html::select("task_type",$tasktypes,$taskgroup->default_task_type);
    //$prior = Html::select("priority",$priority,null);
    $prior = Html::select("priority",$priority,$taskgroup->default_priority);
    //$mem = Html::select("assignee_id",$members,null); // first_
    $mem = Html::select("assignee_id",$members,$taskgroup->default_assignee_id); // first_
    
    $taskgroup_link = $taskgroup->isOwner($w->Auth->user()) ? "<a href=\"".$w->localUrl("task-group/viewmembergroup/".$taskgroup->id)."\">".$taskgroup->title."</a>" : $taskgroup->title; 
    $tasktext = "<table style='width: 100%;'>" .
        "<tr><td class=section colspan=2>Task Group Description</td></tr>" . 
        "<tr><td><b>Task Group</td><td>" . $taskgroup_link . "</td></tr>" . 
        "<tr><td><b>Task Type</b></td><td>" . $typetitle . "</td></tr>" . 
        "<tr valign=top><td><b>Description</b></td><td>" . $typedesc . "</td></tr>" . 
    "</table>";

    // return as array of arrays
    $result = array($ttype, $prior , $mem, $tasktext, Html::select("status", $taskgroup->getTypeStatus(), null, null, null, null));

    $w->setLayout(null);
    $w->out(json_encode($result));
}
