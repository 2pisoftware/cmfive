<?php
class Attachment extends DbObject {
	
	// make it searchable
	var $_searchable;
	var $_exclude_index = array("parent_table","parent_id","mimetype","fullpath","is_deleted");
	
	var $parent_table;
	var $parent_id;

	var $dt_created; // datetime
	var $dt_modified; // datetime
	var $modifier_user_id; // bigint

	var $filename; // varchar(255)
	var $mimetype; // varchar(255)

	var $title; // varchar(255)
	var $description; // text

	var $fullpath; // varchar(255)
	var $is_deleted; // tinyint 0/1

	var $type_code; // this is a type of attachment, eg. Receipt of Deposit, PO Variation, Sitephoto, etc.

	function insert() {
		$this->dt_modified = time();
		$this->mimetype = $this->w->getMimetype(FILE_ROOT."/".$this->fullpath);
		$this->modifier_user_id = $this->w->Auth->user()->id;
		$this->fullpath = str_replace(FILE_ROOT, "", $this->fullpath);
		$this->is_deleted = 0;
		parent::insert();
	}

	function & getParent() {
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
			return WEBROOT."/file/thumb/".$this->fullpath;
		} else {
			return WEBROOT."/img/document.jpg";
		}
	}

	/**
	 *
	 * Returns html code for a thumbnail link to download this attachment
	 */
	function getThumb() {
		$buf = "<a href='".$this->File->getDownloadUrl($this->fullpath)."'>";
		$buf .="<img src='".$this->getThumbnailUrl()."' border='0'/></a>";
		return $buf;
	}

	function getDownloadUrl() {
		return $this->File->getDownloadUrl($this->fullpath);
	}

	function getCodeTypeTitle()
	{
		$t = $this->w->Auth->getObject('AttachmentType', array('code'=>$this->type_code,'table_name'=>$this->parent_table));
		 
		if($t)
		{
			return $t->title;
		}
		else
		{
			return null;
		}
	}

}

