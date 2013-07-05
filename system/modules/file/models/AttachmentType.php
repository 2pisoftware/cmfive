<?php
class AttachmentType extends DbObject {
	var $id;
	var $table_name;
	var $code;
	var $title;
	var $is_active;

	function getDbTableName() {
		return "attachment_type";
	}

	/**
	 * returns the title to be displayed in select boxes
	 * @see web.lib/DbObject::getSelectOptionTitle()
	 */
	function getSelectOptionTitle() {
		return $this->title;
	}

	/**
	 * return the value used in select boxes
	 * @see web.lib/DbObject::getSelectOptionValue()
	 */
	function getSelectOptionValue() {
		return $this->code;
	}

}
