<?php

class ChannelProcessor extends DbObject {

	public $name;
	public $class;
	public $module;
	public $settings;
	public $channel_id;

	public function getChannel() {
		return $this->w->Channel->getChannel($this->channel_id);
	}

}