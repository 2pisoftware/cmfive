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

	public $adapter;
    
	/**
	 * DbObject::insert() override to set the mimetype, path and to call the
	 * attachment hook
	 * 
	 * @param <bool> $force_validation
	 */
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
        return $this->getObject($this->parent_table, $this->parent_id);
    }

    /**
     * will return true if this attachment
     * is an image
	 * 
	 * @return <bool> is_image
     */
    function isImage() {
		// Attachment is an image when the mimetype starts with "image/"
		return strpos($this->mimetype, "image/") === 0;
    }

    /**
     * Returns a HTML <img> tag for this attachment
     * only if this attachment is an image,
     * else
	 * 
	 * @return <String> image_string
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
	 * 
	 * @return <String> url
     */
    function getThumbnailUrl() {
        if ($this->isImage()) {
            return WEBROOT . "/file/atthumb/" . $this->id;
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

	/**********
	 * Gaufrette helper functions
	 **********/
	
	/**
	 * Returns an assembled file path based on the adapter
	 * The local adapter for e.g. needs an absolute reference, this absolute
	 * prefix isn't needed when using S3 buckets
	 * 
	 * @return <String> filepath
	 */
	public function getFilePath() {
		$path = dirname($this->fullpath);

		switch($this->adapter) {
			case "s3":
				if (strpos($path, "uploads/") === FALSE) {
					return "uploads/" . $path;
				}
				return $path;
			default:
				if (strpos($path, FILE_ROOT . "attachments/") !== FALSE) {
					return $path;
				}
				if (strpos($path, "attachments/") !== FALSE) {
					return FILE_ROOT . $path;
				}

				return FILE_ROOT . "attachments/" . $path;
		}
	}
	
	/**
	 * Returns Gaufrette Filsystem instance for fetching files
	 * 
	 * @return \Gaufrette\Filesystem
	 */
	public function getFilesystem() {
		return $this->File->getSpecificFilesystem($this->adapter, $this->getFilePath());
	}
	
	/**
	 * Returns attachment mimetype
	 * @return <String> mimetype
	 */
	public function getMimetype() {
		return $this->mimetype;
	}
	
	/**
	 * Retuns Gaufrette File instance (of the attached file)
	 * @return \Gaufrette\File
	 */
	public function getFile() {
		return new \Gaufrette\File($this->filename, $this->getFilesystem());
	}
	
	/**
	 * Returns attached file content
	 * @return <string> content
	 */
	public function getContent() {
		$file = $this->getFile();
		return $file->exists() ? $file->getContent() : "";
	}
	
	/**
	 * Sends header and content of file to browser
	 */
	public function displayContent() {	
		$this->w->header("Content-Type: " . $this->getMimetype());
		$this->w->out($this->getContent());
	}
	
	/**
	 * Moves the content from one adapter to another
	 */
	public function moveToAdapter($adapter = "local") {
		// Get content of file
		$content = $this->getContent();
		$current_file = $this->getFile();
		
		$this->adapter = $adapter;
		
		$filesystem = $this->getFilesystem();
		$file = new Gaufrette\File($this->filename, $filesystem);
		
		$file->setContent($content);
		
		try {
			$current_file->delete();
		} catch (RuntimeException $ex) {
			$this->w->Log->setLogger("FILE")->error("Cannot delete file: " . $ex->getMessage());
		}
		
		// Update the adapter location
		$this->update(false);
	}
	
	/**
	 * Returns URL to view
	 */
	public function getViewUrl() {
		return "/file/atfile/" . $this->id;
	}
	
}
