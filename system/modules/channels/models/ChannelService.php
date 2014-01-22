<?php

class ChannelService extends DbService {

	public function getChannels() {
		$where = array("is_deleted" => 0);
		return $this->getObjects("Channel", $where);
	}

	public function getChannel($id) {
		return $this->getObject("Channel", $id);
	}

	public function getEmailChannel($channel_id) {
		$where = array('is_deleted' => 0, "channel_id" => $channel_id);
		return $this->getObject('EmailChannelOption', $where);
	}

	public function getEmailChannels() {
		$where = array('is_deleted' => 0);
		return $this->getObjects('EmailChannelOption', $where);
	}

	public function getProcessors() {
		$where = array("is_deleted" => 0);
		return $this->getObjects("ChannelProcessor", $where);
	}

	public function getProcessor($id) {
		$where = array("is_deleted" => 0, "id" => $id);
		return $this->getObject("ChannelProcessor", $where);
	}

	public function getProcessorList() {
		// Get Modules => Processor list
		$list = array();
		$config = $this->w->_moduleConfig;
		foreach($config as $key => $conf) {
			if (array_key_exists("processors", $conf)) {
				// $list[$key] = $conf["processors"];
				foreach($conf["processors"] as $processor) {
					$list[] = $key.".".$processor;
				}
			}
		}

		return $list;
	}

	public function navigation($title,$prenav=null) {
		if ($title) {
			$this->w->ctx("title",$title);
		}
		$nav = $prenav ? $prenav : array();
		if ($this->w->Auth->loggedIn()) {
			$this->w->menuLink("channels/listchannels","List Channels", $nav);
			$this->w->menuLink("channels/listprocessors","List Processors", $nav);
			$this->w->menuLink("channels/listmessages","List Messages", $nav);
		}
		$this->w->ctx("navigation", $nav);
	}

}