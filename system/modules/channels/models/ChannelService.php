<?php

class ChannelService extends DbService {

	public function postMessage() {

	}

	public function retrieveMessage() {

	}

	public function getChannels($where = array()) {
		if (!array_key_exists("is_deleted",	$where))
			$where["is_deleted"] = 0;
		return $this->w->getObjects("channel", $where);
	}

	public function getChannel($id) {
		return $this->w->getObject("channel", $id);
	}

	public function getConfig($channel_name) {
		$globalConf = $this->w->moduleConf("channels", "channel");
        if (!empty($globalConf[$channel_name])) {
            return $globalConf[$channel_name];
        }
        return null;
	}

}