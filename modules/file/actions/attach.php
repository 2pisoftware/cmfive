<?php
function attach_GET(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("table","id","url");
	$object = $w->Auth->getObject($p['table'],$p['id']);
	if (!$object) {
		$w->error("Nothing to attach to.");
	}
	$types = $w->File->getAttachmentTypesForObject($object);
	$w->ctx("types",$types);
}

function attach_POST(Web &$w) {
	$table = $_REQUEST['table'];
	$id = $_REQUEST['id'];
	$title = $_REQUEST['title'];
	$description = $_REQUEST['description'];
	$type_code = $_REQUEST['type_code'];

	$url = str_replace(" ", "/", $_REQUEST['url']);
	$object = $w->Auth->getObject($table,$id);
	if (!$object) {
		$w->error("Nothing to attach to.",$url);
	}

	$aid = $w->service("File")->uploadAttachment("file",$object,$title,$description,$type_code);
	if ($aid) {
		$w->ctx('attach_id',$aid);
		$w->ctx('attach_table',$table);
		$w->ctx('attach_table_id',$id);
		$w->ctx('attach_title',$title);
		$w->ctx('attach_description',$description);
		$w->ctx('attach_type_code',$type_code);
		$w->msg("File attached.",$url);
	} else {
		$w->error("There was an error. Attachment could not be saved.",$url);
	}
}
