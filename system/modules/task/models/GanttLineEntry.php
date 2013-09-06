<?php
class GanttLineEntry extends DbObject {
	public $gantt_id;
	public $line_number;	// SQL Contstraint: UNIQUE (gantt_id, line_number)
	public $parent_id;		// INpublicIANT: if not null, then $this->line_number > $parent->line_number!
	public $level;			// INpublicIANT: $this->level = $parent->level + 1
	public $title;			// if null, $task->title is used for display
	public $task_id;		// if null, $this->title is used, SQL Constraint: UNIQUE (gantt_id, task_id)
	public $css_style;		// css styling for this line
}
