<?php
class GanttLineEntry extends DbObject {
	var $gantt_id;
	var $line_number;	// SQL Contstraint: UNIQUE (gantt_id, line_number)
	var $parent_id;		// INVARIANT: if not null, then $this->line_number > $parent->line_number!
	var $level;			// INVARIANT: $this->level = $parent->level + 1
	var $title;			// if null, $task->title is used for display
	var $task_id;		// if null, $this->title is used, SQL Constraint: UNIQUE (gantt_id, task_id)
	var $css_style;		// css styling for this line
}
