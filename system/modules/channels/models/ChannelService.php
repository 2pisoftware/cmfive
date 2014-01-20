<?php

class ChannelService extends DbService {

	public function getChannel($id) {
		return $this->getObject("Channel", $id);
	}

	public function getEmailChannels() {
		$where = array('is_deleted' => 0);
		return $this->getObjects('EmailChannelOption', $where);
	}


	public function navigation($title,$prenav=null) {
		if ($title) {
			$this->w->ctx("title",$title);
		}
		$nav = $prenav ? $prenav : array();
		if ($this->w->Auth->loggedIn()) {
			$this->w->menuLink("channels/listchannels","List Channels",$nav);
			$this->w->menuLink("channels/listprocessors","List Processors",$nav);

		}
		$this->w->ctx("navigation", $nav);
	}

}