<?php

class ChannelMessage extends DbObject {

    public $channel_id;
    public $message_type;

    // public $is_processed;

    public function getChannel() {
        return $this->w->Channel->getChannel($this->channel_id);
    }

    public function getData() {
        $attachment = $this->w->File->getAttachments($this, $this->id);
        if (!empty($attachment)) {
            return file_get_contents(FILE_ROOT . $attachment[0]->fullpath . $attachment[0]->filename);
        }
        return null;
    }

    public function getStatus($processor_id) {
        return $this->w->Channel->getMessageStatus($this->id, $processor_id);
    }

    public function getFailedProcesses() {
        $resultset = $this->w->db->get("channel_message_status")
                ->where("message_id", $this->id)
                ->where("is_successful", 0);
        if (!empty($resultset)) {
            return $resultset->count();
        }
        return 0;
    }

}
