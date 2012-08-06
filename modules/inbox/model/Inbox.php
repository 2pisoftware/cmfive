<?php
class Inbox extends DbObject {
	var $subject;
	var $user_id;
	var $parent_message_id;
	var $message_id;
	var $dt_created;
	var $dt_read;
	var $is_new;
	var $dt_archived;
	var $is_archived;
	var $has_parent;
	var $sender_id;
	var $del_forever;

	var $_message;

	function & getMessage() {
		if ($this->message_id !== null && !$this->_message) {
			$msg = $this->getObject("Inbox_message", $this->message_id);
			if ($msg) {
				$this->_message = $msg->message;
			}
		}
		return $this->_message;
	}

	function & getSender() {
		if ($this->sender_id) {
			return $this->Auth->getUser($this->sender_id);
		} else {
			return null;
		}
	}

	function & getParentMessage() {
		if(!$this->parent_message_id == 0){
			$message = $this->getMessage($this->parent_message_id);
			$message_arr[$this->parent_message_id] = $message;
			$message->getParentMessage();
		}
		return $message;
	}

}
