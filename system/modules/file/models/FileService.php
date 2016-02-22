<?php

use Gaufrette\Filesystem;
use Gaufrette\File as File;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Adapter\InMemory as InMemoryAdapter;
use Gaufrette\Adapter\AwsS3 as AwsS3;
use Aws\S3\S3Client as S3Client;
/*********************************************
 * Service class with functions to help managing files and attachment records.
 * Encapsulate the use of Gaufrette file system adapters.
 *********************************************/
class FileService extends DbService {

	public static $_thumb_height = 200;
	public static $_thumb_width = 200;
	public static $_stream_name = "attachment";

	/*********************************************
	 * Return the path adjusted to the currently active adapter.
	 * !! This will need a rethink (storing full path in Attachment but also setting the full path here) etc
	 * @return string
	 ********************************************/
	function getFilePath($path) {
		$active_adapter = $this->getActiveAdapter();
		
		switch($active_adapter) {
			case "local":
				if (strpos($path, FILE_ROOT . "attachments/") !== FALSE) {
					return $path;
				}
				if (strpos($path, "attachments/") !== FALSE) {
					return FILE_ROOT . $path;
				}

				return FILE_ROOT . "attachments/" . $path;
			default:
				if (strpos($path, "uploads/") === FALSE) {
					return "uploads/" . $path;
				}
				return $path;
		}
	}

	/*******************************************
	 * Create a new Gaufrette File object from a filename and path
	 * @return File
	 ******************************************/
	function getFileObject($filesystem, $filename) {
		$file = new File($filename, $filesystem);
		return $file;
	}

	/*******************************************
	 * Get the currently active filesystem adapter
	 * The first adapter listed in configuration that is not local is returned.
	 * If local is the only choice (or there are no choices), local is returned as the adapter.
	 * @return string (name of adapter)
	 ******************************************/
	function getActiveAdapter() {
		$adapters = Config::get('file.adapters');
		if (!empty($adapters)) {

			// Omit local because we always default to it
			foreach ($adapters as $adapter => $settings) {
				if ($settings['active'] == true && $adapter !== "local") {
					return $adapter;
				}
			}
		}

		// Always default to local
		return "local";
	}

	/*************************************************************
	 * Get a Gaufrette Filesystem for the currently active adapter and selected path
	 * @return FileSystem
	 ************************************************************/
	function getFilesystem($path = null, $content = null, $options = []) {
		return $this->getSpecificFilesystem($this->getActiveAdapter(), $path, $content, $options);
	}
	/*************************************************************
	 * Get a Gaufrette Filesystem for a given adapter and path
	 * @return FileSystem
	 ************************************************************/	
	function getSpecificFilesystem($adapter = "local", $path = null, $content = null, $options = []) {
		$adapter_obj = null;
		switch ($adapter) {
			case "local":
				$adapter_obj = new LocalAdapter($path, true);
				break;
			case "memory":
				$adapter_obj = new InMemoryAdapter(array(basename($path) => $content));
				break;
			case "s3":
				$config_options = Config::get('file.adapters.s3.options');
				$config_options = array_replace(is_array($config_options) ? $config_options : [], ["directory" => $path], $options);
				$client = S3Client::factory(["key" => Config::get('file.adapters.s3.key'), "secret" => Config::get('file.adapters.s3.secret')]);
				$adapter_obj = new AwsS3($client, Config::get('file.adapters.s3.bucket'), is_array($config_options) ? $config_options : []);
				break;
		}

		if ($adapter_obj !== null) {
			return new Filesystem($adapter_obj);
		}
		return null;
	}

	/*************************************************************
	 * Register a gaufrette stream wrapper
	 * @return 
	 ************************************************************/	
	function registerStreamWrapper($filesystem = null) {
		if (!empty($filesystem)) {
			$map = \Gaufrette\StreamWrapper::getFilesystemMap();
			$map->set(self::$_stream_name, $filesystem);

			\Gaufrette\StreamWrapper::register();
		}
	}

	/*************************************************************
	 * Create a HTML image tag for the image specified by $path
	 * @return string(html img tag)
	 ************************************************************/	
	function getImg($path) {
		$file = FILE_ROOT . $path;
		if (!file_exists($file))
			return null;

		list($width, $height, $type, $attr) = getimagesize($file);

		$tag = "<img src='" . WEBROOT . "/file/path/" . $path . "' border='0' " . $attr . " />";
		return $tag;
	}

	/*************************************************************
	 * Create a HTML image tag for a thumbnail of the image specified by $path
	 * @return string(html img tag)
	 ************************************************************/	
	function getThumbImg($path) {
		$file = FILE_ROOT . $path;
		if (!file_exists($file))
			return $path . " does not exist.";

		list($width, $height, $type, $attr) = getimagesize($file);

		$tag = "<img src='" . WEBROOT . "/file/thumb/" . $path . "' height='" . self::$_thumb_height . "' width='" . self::$_thumb_width . "' />";
		return $tag;
	}
	
	/*************************************************************
	 * Check if an attachment is an image
	 * @return boolean
	 ************************************************************/	
	function isImage($path) {
		if (file_exists(str_replace("'", "\\'", FILE_ROOT . "/" . $path))) {
			$path = str_replace("'", "\\'", FILE_ROOT . "/" . $path);
			$attr = null;
			if (is_file($path)) {
				list($width, $height, $type, $attr) = getimagesize($path);
			}
			return $attr !== null;
		} else {
			return false;
		}
	}
	
	/*************************************************************
	 * Get a url to the file specified by $path
	 * @return string(html img tag)
	 ************************************************************/	
	function getDownloadUrl($path) {
		return WEBROOT . "/file/path/" . $path;
	}

	/*************************************************************
	 * Lookup the attachments for a given object
	 * @return [string]  (full paths to attachments)
	 ************************************************************/	
	function getAttachmentsFileList($objectOrTable, $id = null) {
		$attachments = $this->getAttachments($objectOrTable, $id);
		if (!empty($attachments)) {
			$pluck = array();
			array_reduce($attachments, function(&$pluck, $attachment) {
				$pluck[] = $attachment->fullpath;
			});
			return $pluck;
		}
		return array();
	}
	
	/*************************************************************
	 * Lookup the attachments for a given object
	 * @return [Attachment]
	 ************************************************************/	
	function getAttachments($objectOrTable, $id = null) {
		if (is_scalar($objectOrTable)) {
			$table = $objectOrTable;
		} elseif (is_a($objectOrTable, "DbObject")) {
			$table = $objectOrTable->getDbTableName();
			$id = $objectOrTable->id;
		}
		if ($table && $id) {
			$rows = $this->_db->get("attachment")->where("parent_table", $table)->and("parent_id", $id)->and("is_deleted", 0)->fetch_all();
			return $this->fillObjects("Attachment", $rows);
		}
		return null;
	}

	/*************************************************************
	 * Load a single attachment
	 * @return Attachment
	 ************************************************************/	
	function getAttachment($id) {
		return $this->getObject("Attachment", $id);
	}

	/*************************************************************
	 * Move an uploaded file from the temp location
	 * to
	 *  /files/attachments/<attachTable>/<year>/<month>/<day>/<attachId>/<filename>
	 * 
	 * and create an Attachment record.
	 * 
	 * @param <type> $filename
	 * @param <type> $attachTable
	 * @param <type> $attachId
	 * @param <type> $title
	 * @param <type> $description
	 * @return the id of the attachment object or null
	 *************************************************************/
	function uploadAttachment($requestkey, $parentObject, $title = null, $description = null, $type_code = null) {
		if (!is_a($parentObject, "DbObject")) {
			$this->w->error("Parent not found.");
		}

		$replace_empty = array("..", "'", '"', ",", "\\", "/");
		$replace_underscore = array(" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":");
		
		//Check for posted content
		if(!empty($_POST[$requestkey])) {
			$filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", $_POST['fname']));
		} else {
			$filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", basename($_FILES[$requestkey]['name'])));
		}

		$att = new Attachment($this->w);
		$att->filename = $filename;
		$att->fullpath = null;
		$att->parent_table = $parentObject->getDbTableName();
		$att->parent_id = $parentObject->id;
		$att->title = $title;
		$att->description = $description;
		$att->type_code = $type_code;
		$att->insert();

		$filesystemPath = "attachments/" . $parentObject->getDbTableName() . '/' . date('Y/m/d') . '/' . $att->id . '/';
		$filesystem = $this->getFilesystem($this->getFilePath($filesystemPath));
		$file = new File($filename, $filesystem);
		
		$att->adapter = $this->getActiveAdapter();
		$att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);
		
		//Check for posted content
		if(!empty($_POST[$requestkey])) {
			preg_match('%data:(.*);base%', substr($_POST[$requestkey], 0, 25), $mime);
			$data = substr($_POST[$requestkey], strpos($_POST[$requestkey], ",") + 1);
			$mime_type = $mime[1];
			$content = base64_decode($data);
		} else {
			$content = file_get_contents($_FILES[$requestkey]['tmp_name']);
			
			switch($this->adapter) {
				case "local":
					$mime_type = $this->w->getMimetype(FILE_ROOT . $att->fullpath);
				default:
					$mime_type = $this->w->getMimetypeFromString($content);
			}
		}
		$file->setContent($content, ['contentType' => $mime_type]);
		
		$att->mimetype = $mime_type;		
		$att->update();
		return $att->id;
	}

	/**
	 * Uploads multiple attachments at once (Using the Html::multiFileUpload function
	 *
	 *  Stores in /uploads/attachments/<ObjectTableName>/<year>/<month>/<day>/<attachId>/<filename>
	 *
	 * @param <string> $requestKey
	 * @param <DbObject> $parentObject
	 * @param <Array> $titles
	 * @param <Array> $descriptions
	 * @param <Array> $type_codes
	 * @return <bool> if upload was successful
	 */
	function uploadMultiAttachment($requestkey, $parentObject, $titles = null, $descriptions = null, $type_codes = null) {
		if (!is_a($parentObject, "DbObject")) {
			$this->w->error("Parent object not found.");
			return false;
		}

		$rpl_nil = array("..", "'", '"', ",", "\\", "/");
		$rpl_ws = array(" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":");

		if (!empty($_FILES[$requestkey]['name']) && is_array($_FILES[$requestkey]['name'])) {
			$file_index = 0;
			foreach ($_FILES[$requestkey]['name'] as $FILE_filename) {
				// Files can be empty
				if (!empty($FILE_filename['file'])) {
					$filename = str_replace($rpl_ws, "_", str_replace($rpl_nil, "", basename($FILE_filename['file'])));

					$att = new Attachment($this->w);
					$att->filename = $filename;
					$att->fullpath = null;
					$att->parent_table = $parentObject->getDbTableName();
					$att->parent_id = $parentObject->id;
					$att->title = (!empty($titles[$file_index]) ? $titles[$file_index] : '');
					$att->description = (!empty($descriptions[$file_index]) ? $descriptions[$file_index] : '');
					$att->type_code = (!empty($type_codes) ? $type_codes[$file_index] : '');
					$att->insert();

					$filesystemPath = FILE_ROOT . "attachments/" . $parentObject->getDbTableName() . '/' . date('Y/m/d') . '/' . $att->id . '/';
					$filesystem = $this->getFilesystem($filesystemPath);
					$file = new File($filename, $filesystem);
					$file->setContent(file_get_contents($_FILES[$requestkey]['tmp_name'][$file_index]['file']));

					$att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);
					$att->update();
				}

				$file_index++;
			}
		}

		return true;
	}

	/*************************************************************
	 * Save an attachment and create a file based on content passed as a parameter
	 * @return integer  (new attachment id)
	 ************************************************************/	
	function saveFileContent($object, $content, $name = null, $type_code = null, $content_type = null) {

		$filename = (!empty($name) ? $name : (str_replace(".", "", microtime()) . getFileExtension($content_type)));

		$filesystemPath = $object->getDbTableName() . '/' . date('Y/m/d') . '/' . $object->id . '/';

		$filesystem = $this->getFilesystem($filesystemPath);
		$file = new File($filename, $filesystem);
		$file->setContent($content);

		$att = new Attachment($this->w);
		$att->filename = $filename;
		$att->fullpath = str_replace(FILE_ROOT, "", $this->getFilePath($filesystemPath) . (substr($this->getFilePath($filesystemPath), -1) !== '/' ? '/' : '') . $att->filename);
		$att->parent_table = $object->getDbTableName();
		$att->parent_id = $object->id;
		$att->title = $filename;
		$att->type_code = $type_code;
		$att->mimetype = $content_type;
//                $att->modifier_user_id = $this->w->Auth->user()->id;
		$att->insert();

		return $att->id;
	}
	
	/*************************************************************
	 * Get the attachment types for a given object type
	 * @return [AttachmentType]
	 ************************************************************/	
	function getAttachmentTypesForObject($o) {
		return $this->getObjects("AttachmentType", array("table_name" => $o->getDbTableName(), "is_active" => '1'));
	}

	/*************************************************************
	 * Render a template showing an attachment
	 * @return string  
	 ************************************************************/	
	function getImageAttachmentTemplateForObject($object, $backUrl) {
		$attachments = $this->getAttachments($object);
		$template = "";
		foreach ($attachments as $att) {
			if ($att->isImage()) {
				$template .= '
				<div class="attachment">
				<div class="thumb"><a
					href="' . WEBROOT . '/file/atthumb/' . $att->id . '/800/600/a.jpg"
					rel="gallery"><img
					src="' . WEBROOT . '/file/atthumb/' . $att->id . '/250/250" border="0" /></a><br/>' . $att->description . '
				</div>
				
				<div class="actions">' . Html::a(WEBROOT . "/file/atdel/" . $att->id . "/" . $backUrl . "+" . $object->id, "Delete", null, null, "Do you want to delete this attachment?")
						. ' ' . Html::a(WEBROOT . "/file/atfile/" . $att->id . "/" . $att->filename, "Download") . '
				</div>
				</div>';
			}
		}
		return $template;
	}

}
