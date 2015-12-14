<?php

class Attachment extends DbObject {

    // make it searchable
    public $_searchable;
    public $_exclude_index = array("parent_table", "parent_id", "mimetype", "fullpath", "is_deleted");
    public $parent_table;
    public $parent_id;
    public $dt_created; // datetime
    public $dt_modified; // datetime
    public $modifier_user_id; // bigint
    public $filename; // publicchar(255)
    public $mimetype; // publicchar(255)
    public $title; // publicchar(255)
    public $description; // text
    public $fullpath; // publicchar(255)
    public $is_deleted; // tinyint 0/1
    public $type_code; // this is a type of attachment, eg. Receipt of Deposit, PO Variation, Sitephoto, etc.

    function insert($force_validation = false) {
        // $this->dt_modified = time();
        // Get mimetype

        if (empty($this->mimetype)) {
            $this->mimetype = $this->w->getMimetype(FILE_ROOT . "/" . $this->fullpath);
        }
        // $this->modifier_user_id = $this->w->Auth->user()->id; <-- why?
        $this->fullpath = str_replace(FILE_ROOT, "", $this->fullpath);

        // $this->filename = ($this->filename . getFileExtension($this->mimetype));

        $this->is_deleted = 0;
        parent::insert($force_validation);
        
        $this->w->callHook("attachment", "attachment_added_" . $this->parent_table, $this);
    }

    function getParent() {
        return $this->getObject($this->attach_table, $this->attach_id);
    }

    /**
     * will return true if this attachment
     * is an image
     */
    function isImage() {
        return $this->File->isImage($this->fullpath);
    }

    /**
     * Returns a HTML <img> tag for this attachment
     * only if this attachment is an image,
     * else
     */
    function getImg() {
        if ($this->isImage()) {
            return $this->File->getImg($this->fullpath);
        } else {
            
        }
    }

    /**
     * if image, create image thumbnail
     * if any other file send an icon for this mimetype
     */
    function getThumbnailUrl() {
        if ($this->isImage()) {
            return WEBROOT . "/file/thumb/" . $this->fullpath;
        } else {
            return WEBROOT . "/img/document.jpg";
        }
    }

    /**
     *
     * Returns html code for a thumbnail link to download this attachment
     */
    function getThumb() {
        return Html::box($this->File->getDownloadUrl($this->fullpath), $this->File->getThumbImg($this->fullpath));
    }

    function getDownloadUrl() {
        return $this->File->getDownloadUrl($this->fullpath);
    }

    function getCodeTypeTitle() {
        $t = $this->w->Auth->getObject('AttachmentType', array('code' => $this->type_code, 'table_name' => $this->parent_table));

        if ($t) {
            return $t->title;
        } else {
            return null;
        }
    }

}
