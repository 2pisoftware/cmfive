<?php
class GanttChart extends DbObject {
	var $title;
	var $_modifiable;		// modifiable aspect
	var $task_group_id;		// a Gantt chart is always linked to a task group
	var $can_view;			// PRIVATE/OWNER/MEMBER/GUEST/ALL , if PRIVATE only the creator can view!
	var $can_edit;			// PRIVATE/OWNER/MEMBER/GUEST/ALL
}
