<?php 

function listtaskgroups_ALL(Web $w, $params = array()) {
    $w->ctx("taskgroups", $params['taskgroups']);
    $w->ctx("redirect", $params['redirect']);
}
