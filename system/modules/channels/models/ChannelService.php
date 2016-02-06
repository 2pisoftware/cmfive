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
	 * Returns a non-deleted email channel object
	 * @return Object emailchannel
	 */
	public function getWebChannel($channel_id) {
		$where = array('is_deleted' => 0, "channel_id" => $channel_id);
		return $this->getObject('WebChannelOption', $where);
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
	 * Returns all non-deleted email channel objects
	 * @return Array<EmailChannelOption> emailchannels
	 */
	public function getWebChannels() {
		$where = array('is_deleted' => 0);
		return $this->getObjects('WebChannelOption', $where);
	}

	/**
	 * Returns all non-deleted email channel objects
	 * @return Array<EmailChannelOption> emailchannels
	 */
	public function getChildChannel($id) {
            $child_channel = $this->getEmailChannel($id);
            if (!isset($child_channel)) {
                $child_channel = $this->getWebChannel($id);
            }
            
            return $child_channel;
	}

        /**
	 * Returns all non-deleted processor objects
	 * @return Array<ChannelProcessor> processors
	 */
	public function getProcessors($channel_id = null) {
            if (empty($channel_id)) {
                return null;
            }
            $where = array("is_deleted" => 0, "channel_id" => $channel_id);
            return $this->getObjects("ChannelProcessor", $where);
	}

        public function getAllProcessors() {
            return $this->getObjects("ChannelProcessor", array("is_deleted" => 0));
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
            foreach($this->w->modules() as $module) {
                $processors = Config::get("{$module}.processors");
                if (!empty($processors)) {
                    foreach($processors as $processor) {
                        $list[] = $module.".".$processor;
                    }
                }
            }

            return $list;
	}

	public function getMessages($channel_id = null, $include_deleted = false) {
		$where = array();
		if ($include_deleted === false) {
			$where["is_deleted"] = 0;
		}
		if (!empty($channel_id)) {
			$where["channel_id"] = $channel_id;
		}

		return $this->getObjects("ChannelMessage", $where, false, true, "dt_created desc");
	}
	
	public function getNewMessages($channel_id, $processor_id) {
		$query = $this->w->db->get("channel_message")->where("channel_message.channel_id", $channel_id)
								->leftJoin("channel_message_status on channel_message_status.message_id = channel_message.id")
								->where("channel_message_status.id IS NULL OR channel_message_status.processor_id != ?", $processor_id)
								->fetch_all();
					
		if (!empty($query)) {
			return $this->getObjectsFromRows("ChannelMessage", $query);
		}
		return null;
	}

	public function getMessage($id) {
		return $this->getObject("ChannelMessage", $id);
	}

	public function getMessageStatus($message_id, $processor_id = null) {
		$where = array("message_id" => $message_id);
		if (!empty($processor_id)) {
			$where["processor_id"] = $processor_id;
		}

		return $this->getObject("ChannelMessageStatus", $where);
	}

	public function getMessageStatuses($message_id) {
		$where = array("message_id" => $message_id);
		return $this->getObjects("ChannelMessageStatus", $where);
	}

	/**
	 * Channels naivgation function
	 * @return none
	 */
    public function navigation(Web $w, $title = null, $prenav=null) {
		if ($title) {
			$w->ctx("title",$title);
		}
		$nav = $prenav ? $prenav : array();
		if ($w->Auth->loggedIn()) {
			$w->menuLink("channels/listchannels","List Channels", $nav);
			$w->menuLink("channels/listprocessors","List Processors", $nav);
			$w->menuLink("channels/listmessages","List Messages", $nav);
		}
		$w->ctx("navigation", $nav);
        return $nav;
	}

}