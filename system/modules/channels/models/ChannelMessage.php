<?php

class ChannelMessage extends DbObject {

	public $channel_id;
	public $message_type;
	public $is_processed;

	public function getChannel() {
		return $this->w->Channel->getChannel($this->channel_id);
	}

	public function getData() {
		$attachment = $this->w->File->getAttachments($this, $this->id);
		return file_get_contents(FILE_ROOT . $attachment[0]->fullpath . $attachment[0]->filename);
	}

}