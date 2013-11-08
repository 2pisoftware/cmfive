<?php
function listcomments_ALL(Web $w, $params) {
	$object = $params['object'];
	$redirect = $params['redirect'];
	$w->ctx("comments",$w->Comment->getCommentsForTable($object->getDbTableName(), $object->id));
	$w->ctx("redirect",$redirect);
	$w->ctx("object",$object);
}