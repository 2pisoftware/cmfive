<?php
class TaskComment extends AComment {

	// Notifier is called directly because notification should be done for comments inserted from 
	// post listener as well.
	function insert(){
		parent::insert();
		
		$this->w->ctx('comment_id',$this->id);
		
		if(!$this->is_system)
		{
			//$notifier = new TaskNotifier($this->w);
			//$notifier->notify(null,'comment');
		}
	}
}

