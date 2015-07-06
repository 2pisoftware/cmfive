<?php

function timelogwidget_ALL(Web $w) {
    $active_log = $w->Timelog->getActiveTimelogForUser();
    $w->ctx("active_log", $active_log);
}