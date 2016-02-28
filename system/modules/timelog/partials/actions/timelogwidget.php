<?php namespace System\Modules\Timelog;

function timelogwidget(\Web $w) {
    $active_log = $w->Timelog->getActiveTimelogForUser();
    $w->ctx("active_log", $active_log);
}