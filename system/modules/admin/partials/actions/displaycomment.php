<?php

function displaycomment_ALL(Web $w, $params) {
    if (!empty($params['redirect'])) {
        $w->ctx("redirect", $params['redirect']);
    }
    if (!empty($params['displayOnly'])) {
        $w->ctx("displayOnly", true);
    }
    $w->ctx("c", $params['object']);
}
