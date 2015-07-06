<?php

define('DEFAULT_PAGE', 1);
define('DEFAULT_PAGE_SIZE', 30);

function index_GET(Web $w) {
    $p = $w->pathMatch('page', 'pagesize');
    $page = (!empty($p['page']) ? $p['page'] : DEFAULT_PAGE);
    $pagesize = (!empty($p['pagesize']) ? $p['pagesize'] : DEFAULT_PAGE_SIZE);
    
    $timelog = $w->Timelog->getTimelogsForUser(); // $w->Task->getTaskTimes();
    $totalresults = count($timelog);

    $w->ctx('pagination', Html::pagination($page, (ceil($totalresults / $pagesize)), $pagesize, $totalresults, '/task-time'));
    
    $time_entry_objects = array();
    $paged_timelogs = array_slice($timelog, (($page - 1) * $pagesize), $pagesize);
    if (!empty($paged_timelogs)) {
        foreach($paged_timelogs as $time_entry) {
            
            $entry_date = date('d/m', $time_entry->dt_start);
            if (empty($time_entry_objects[$entry_date])) {
                $time_entry_objects[$entry_date] = array('entries' => array(), "total" => 0);
            }
            
            $time_entry_objects[$entry_date]['total'] += $time_entry->getDuration();
            $time_entry_objects[$entry_date]['entries'][] = $time_entry; 
        }
    }
    $w->ctx('time_entries', $time_entry_objects);
}

function index_POST(Web $w) {
    
}
