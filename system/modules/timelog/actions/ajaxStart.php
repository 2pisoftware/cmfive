<?php

function ajaxStart_POST(Web $w) {
    if ($w->Timelog->hasActiveLog()) {
        $w->Log->debug("active log exists");
		
        return "0";
    }
    
    $p = $w->pathMatch("class", "id");
    
    if (!class_exists($p['class'])) {
        $w->Log->debug("class " . $p['class'] . " doesnt exist");
        return "0";
    }
    
    $object = $w->Timelog->getObject($p['class'], $p['id']);

    if (!empty($object->id)) {
        $timelog = new Timelog($w);
        $timelog->start($object);
		
		if (!empty($_POST['description'])) {
			$timelog->setComment($_POST['description']);
		}
		
        echo json_encode([
            'object'    => $p['class'],
            'title'     => $object->getSelectOptionTitle()
        ]);
//        $w->Comment->addComment($timelog, $w->request('description'));
    } else {
        $w->Log->debug("object not found");
		return "0";
    }
}