<?php

use Gaufrette\Filesystem;
use Gaufrette\File as File;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Adapter\InMemory as InMemoryAdapter;

class FileService extends DbService {
    
    public static $_thumb_height = 200;
    public static $_thumb_width = 200;
    public static $_stream_name = "attachment";

    // This will need a rethink (storing full path in Attachment but also setting the full path here) etc
    function getFilePath($path) {
    	if (strpos($path, FILE_ROOT . "attachments/") !== FALSE){
    		return $path;
    	}
    	return FILE_ROOT . "attachments/" . dirname($path);
    }

    function getFileObject($filesystem, $filename) {
    	$file = new File(basename($filename), $filesystem);
    	return $file;
    }

    function getFilesystem($path = null, $adapter = "local", $content = null) {
    	$adapter_obj = null;
    	switch ($adapter){
    		case "local":
    			$adapter_obj = new LocalAdapter($this->getFilePath($path), true);
    			break;
    		case "memory":
    			$adapter_obj = new InMemoryAdapter(array(basename($path) => $content));
    			break;
    	}
    		
    	if ($adapter_obj !== null){
    		return new Filesystem($adapter_obj);
    	}
    	return null;
    }

    function registerStreamWrapper($filesystem = null) {
    	if (!empty($filesystem)){
    		$map = \Gaufrette\StreamWrapper::getFilesystemMap();
    		$map->set(self::$_stream_name, $filesystem);

    		\Gaufrette\ StreamWrapper::register();
    	}
    }

	function getImg($path) {
		$file = FILE_ROOT.$path;
		if (!file_exists($file))
		return null;

		list($width, $height, $type, $attr) = getimagesize($file);

		$tag = "<img src='".WEBROOT."/file/path/".$path."' border='0' ".$attr." />";
		return $tag;
	}

	function getThumbImg($path) {
		$file = FILE_ROOT.$path;
		if (!file_exists($file))
		return $path." does not exist.";

		list($width, $height, $type, $attr) = getimagesize($file);
                
		$tag = "<img src='".WEBROOT."/file/thumb/".$path."' height='".self::$_thumb_height."' width='".self::$_thumb_width."' />";
		return $tag;
	}

	function isImage($path) {
            	if (file_exists(str_replace("'","\\'",FILE_ROOT."/".$path))) {
			list($width, $height, $type, $attr) = getimagesize(str_replace("'","\\'",FILE_ROOT."/".$path));
			return $attr !== null;
		} else {
			return false;
		}
	}

	function getDownloadUrl($path) {
		return WEBROOT."/file/path/".$path;
	}


	function getAttachments($objectOrTable,$id=null) {
		if (is_scalar($objectOrTable)) {
			$table = $objectOrTable;
		} elseif (is_a($objectOrTable, "DbObject")) {
			$table = $objectOrTable->getDbTableName();
			$id = $objectOrTable->id;
		}
		if ($table && $id) {
			$rows = $this->_db->get("attachment")->where("parent_table",$table)->and("parent_id",$id)->and("is_deleted",0)->fetch_all();
			return $this->fillObjects("Attachment", $rows);
		}
		return null;
	}

	function getAttachment($id) {
		return $this->getObject("Attachment", $id);
	}

	/**
	 * moves an uploaded file from the temp location
	 * to
	 *
	 *  /files/attachments/<attachTable>/<year>/<month>/<day>/<attachId>/<filename>
	 *
	 * @param <type> $filename
	 * @param <type> $attachTable
	 * @param <type> $attachId
	 * @param <type> $title
	 * @param <type> $description
	 * @return the id of the attachment object or null
	 */
	function uploadAttachment($requestkey,$parentObject,$title=null,$description=null,$type_code=null) {
		if (!is_a($parentObject, "DbObject")) {
			$this->w->error("Parent not found.");
		}
		// we could check if the attach id actually exists
		// but will leave this for later
		// $uploaddir = FILE_ROOT. 'attachments/'.$parentObject->getDbTableName().'/'.date('Y/m/d').'/'.$parentObject->id.'/';
		// if (!file_exists($uploaddir)) {
		// 	mkdir($uploaddir,0770,true);
		// }
		$rpl_nil = array("..","'",'"',",","\\","/");
		$rpl_ws = array(" ","&","+","$","?","|","%","@","#","(",")","{","}","[","]",",",";",":");
		$filename = str_replace($rpl_nil, "", basename($_FILES[$requestkey]['name']));
		$filename = str_replace($rpl_ws, "_", $filename);
		// $uploadfile = $uploaddir . $filename;

		// if (move_uploaded_file($_FILES[$requestkey]['tmp_name'], $uploadfile)) {
		$filesystemPath = $parentObject->getDbTableName().'/'.date('Y/m/d').'/'.$parentObject->id;
		$filesystem = $this->getFilesystem($filesystemPath);
		$file = new File($filename, $filesystem);
		$file->setContent(file_get_contents($_FILES[$requestkey]['tmp_name']));

		$att = new Attachment($this->w);
		$att->filename = $filename;
		$att->fullpath = str_replace(FILE_ROOT, "", $this->getFilePath($filesystemPath) . "/" . $filename);
		$att->parent_table = $parentObject->getDbTableName();
		$att->parent_id = $parentObject->id;
		$att->title = $title;
		$att->description = $description;
		$att->type_code = $type_code;
		$att->insert();
		return $att->id;
		// } else {
		// 	$this->w->error("Possible file upload attack.");
		// }
		// return null;
	}

	function saveFileContent($object, $content, $name = null, $type_code = null, $content_type = null) {

		$filename = (!empty($name) ? $name : (str_replace(".", "", microtime()) . getFileExtension($content_type)));

		$filesystemPath = $object->getDbTableName().'/'.date('Y/m/d').'/'.$object->id . '/';
		$filesystem = $this->getFilesystem($filesystemPath);
		$file = new File($filename, $filesystem);
		$file->setContent($content);

		$att = new Attachment($this->w);
		$att->filename = $filename;
		$att->fullpath = str_replace(FILE_ROOT, "", $this->getFilePath($filesystemPath) . "/" . $this->filename);
		$att->parent_table = $object->getDbTableName();
		$att->parent_id = $object->id;
		$att->title = $filename;
		$att->type_code = $type_code;
		$att->mimetype = $content_type;
//                $att->modifier_user_id = $this->w->Auth->user()->id;
		$att->insert();

		return $att->id;
	}

	function getAttachmentTypesForObject($o) {
		return $this->getObjects("AttachmentType",array("table_name"=>$o->getDbTableName(), "is_active"=>'1'));
	}

	function getImageAttachmentTemplateForObject($object,$backUrl) {
		$attachments = $this->getAttachments($object);
		$template = "";
		foreach($attachments as $att) {
			if ($att->isImage()) {
				$template .= '
				<div class="attachment">
				<div class="thumb"><a
					href="'.WEBROOT.'/file/atthumb/'.$att->id.'/800/600/a.jpg"
					rel="gallery"><img
					src="'.WEBROOT.'/file/atthumb/'.$att->id.'/250/250" border="0" /></a><br/>'.$att->description.'
				</div>
				
				<div class="actions">'.Html::a(WEBROOT."/file/atdel/".$att->id."/".$backUrl."+".$object->id,"Delete",null,null,"Do you want to delete this attachment?")
				.' '.Html::a(WEBROOT."/file/atfile/".$att->id."/".$att->filename,"Download").'
				</div>
				</div>';
			}
		}
		return $template;
	}

}
