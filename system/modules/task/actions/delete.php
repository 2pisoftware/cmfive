<?php

function delete_ALL(Web &$w) {
    $p = $w->pathMatch("id");

    // task is to get updated so gather relevant data
    $task = $w->Task->getTask($p['id']);

    // if task exists, continue
    if (!empty($task->id)) {
        $task->is_closed = 1;
        $task->is_deleted = 1;
        $task->update();
        $w->msg("Task: " . $task->title . " has been deleted.", "/task/tasklist/");
    } else {
        $w->error("Task could not be found.", "/task/tasklist/");
    }
}
