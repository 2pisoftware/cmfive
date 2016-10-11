<?php namespace System\Modules\Timelog;

function timelogwidget(\Web $w) {
	
    $w->ctx("active_log", $w->Timelog->getActiveTimelogForUser());
    
    $tracked_object = $w->Timelog->hasTrackingObject() ? $w->timelog->getTrackingObject() : null;
    $info = '';
    if (!empty($tracked_object)) {
        //generate title and description
        //check if task
        if ($w->Timelog->getTrackingObjectClass() == 'Task') {
            $info .= "<h3>Task [";
            $info .= $tracked_object->id;
            $info .= "]: ";
            $info .= $tracked_object->title;
            $info .= "</h3>";
            $info .= "<blockquote>Created: ";
            $info .= formatDate($tracked_object->_modifiable->getCreatedDate());
            $info .= (!empty($tracked_object->_modifiable->getCreator()) ? ' by <strong>' . @$tracked_object->_modifiable->getCreator()->getFullName() . '</strong>' : '');
            $info .= "<br/>Taskgroup: ";
            $info .= $tracked_object->getTaskGroupTypeTitle();
            $info .= "</blockquote>";
        } else {
            $info .= "<h3>";
            $title = $tracked_object->printSearchTitle();
            if (!empty($title)) {
                $info .= $title;
            } else {
                $info .= $w->Timelog->getTrackingObjectClass();
                $info .= " [";
                $info .= $tracked_object->id;
                $info .= "] ";
            }
            $info .= "</h3>";
        }        
    }
    $w->ctx('active_object_description', $info);
}