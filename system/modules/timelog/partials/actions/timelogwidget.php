<?php namespace System\Modules\Timelog;

function timelogwidget(\Web $w) {
	
    $w->ctx("active_log", $w->Timelog->getActiveTimelogForUser());
	
}