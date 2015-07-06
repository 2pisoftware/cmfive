<?php

function ajaxStart_GET(Web $w) {
    if ($w->Timelog->hasActiveLog()) {
        $this->w->Log->debug("active log exists");
        return;
    }
    
    $p = $w->pathMatch("class", "id");
    
    if (!class_exists($p['class'])) {
        $this->w->Log->debug("class " . $p['class'] . " doesnt exist");
        return;
    }
    
    $object = $w->Timelog->getObject($p['class'], $p['id']);
    
    if (!empty($object->id)) {
        $timelog = new Timelog($w);
        $timelog->start($object);
    
//        $w->Comment->addComment($timelog, $w->request('description'));
    } else {
        $this->w->Log->debug("object not found");
    }
}