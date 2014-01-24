<?php

class ChannelService extends DbService {

	/**
	 * Returns all non-deleted channel objects
	 * @return Array<Channel> channels
	 */
	public function getChannels() {
		$where = array("is_deleted" => 0);
		return $this->getObjects("Channel", $where);
	}

	/**
	 * Returns a non-deleted channel object
	 * @return Object channel
	 */
	public function getChannel($id) {
		return $this->getObject("Channel", $id);
	}

	/**
	 * Returns a non-deleted email channel object
	 * @return Object emailchannel
	 */
	public function getEmailChannel($channel_id) {
		$where = array('is_deleted' => 0, "channel_id" => $channel_id);
		return $this->getObject('EmailChannelOption', $where);
	}

	/**
	 * Returns all non-deleted email channel objects
	 * @return Array<EmailChannelOption> emailchannels
	 */
	public function getEmailChannels() {
		$where = array('is_deleted' => 0);
		return $this->getObjects('EmailChannelOption', $where);
	}

	/**
	 * Returns all non-deleted processor objects
	 * @return Array<ChannelProcessor> processors
	 */
	public function getProcessors($channel_id = null) {
		$where = array("is_deleted" => 0);
		if (!empty($channel_id)) {
			$where["channel_id"] = $channel_id;
		}
		return $this->getObjects("ChannelProcessor", $where);
	}

	/**
	 * Returns a non-deleted processor object
	 * @return Object processor
	 */
	public function getProcessor($id) {
		$where = array("is_deleted" => 0, "id" => $id);
		return $this->getObject("ChannelProcessor", $where);
	}

	/**
	 * Returns a parsed list of available processors
	 * @return Array list
	 */
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

	public function getMessages($channel_id = null) {
		$where = array("is_deleted" => 0);
		if (!empty($channel_id)) {
			$where["channel_id"] = $channel_id;
		}

		return $this->getObjects("ChannelMessage", $where);
	}

	public function getMessage($id) {
		return $this->getObject("ChannelMessage", $id);
	}


	/**
	 * Aux Channels naivgation function
	 * @return none
	 */
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