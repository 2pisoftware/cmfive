<?php // echo $w->partial("listtaskgroups", array("taskgroups" => $taskgroups, "redirect" => "/tasks", "should_filter" => true, "filter_closed_tasks" => true), "task"); ?>

<div class="row-fluid">
    <div class="small-12 columns panel">
        <h4>
            You're a member of <b><?php echo count($taskgroups); ?> taskgroup<?php echo count($taskgroups) == 1 ? "" : "s"; ?></b><br/>
            With <b><?php echo Html::a($w->localUrl("/task/tasklist"), $count_taskgroup_tasks . " task" . ($count_taskgroup_tasks == 1 ? "" : "s")) ?></b> overall
        </h4>
    </div>
</div>

<div class="row-fluid" data-equalizer>
    <div class="small-12 medium-12 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <h2 class="text-center"><?php echo Html::a($w->localUrl("/task/tasklist?assignee_id=" . $w->Auth->user()->id), (count($tasks) . " Task" . (count($tasks) == 1 ? "" : "s"))); ?> <small>assigned to you</small></h2>
            <hr style="margin: 5px 0px;"/>
            <p class="text-center" style="margin-bottom: 0px;">out of <?php echo $total_tasks; ?> total task<?php echo $total_tasks == 1 ? "" : "s"; ?></p>
        </div>
    </div>
    <div class="small-12 medium-6 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <?php if ($count_overdue > 0) : ?>
            <h2 class="text-center"><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_to=" . formatDate(time(), "Y-m-d"), $count_overdue . " overdue"); ?></h2>
            <?php endif; ?>
            <?php if ($count_due_soon == 0) : ?>
                <h4 class='text-center'><b>0 due</b> within 7 days</h4>
            <?php else: ?>
                <h4 class="text-center"><b><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_from=" . formatDate(time(), 'Y-m-d') . "&dt_to=" . formatDate((time() + (60 * 60 * 24 * 7)), "Y-m-d"), $count_due_soon . " due"); ?></b> within 7 days</h4>
            <?php endif; ?>
            <?php if ($count_no_due_date > 0) : ?>
                <hr style="margin: 5px 0px;"/>
                <p class="text-center"><?php echo Html::a("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&dt_to=NULL", $count_no_due_date); ?> without a due date</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="small-12 medium-6 large-4 columns panel" data-equalizer-watch>
        <div style='position: relative; top: 50%; transform: translateY(-50%);'>
            <h2 class="text-center">
                <?php if ($count_todo_urgent == 0) : ?>
                    <?php echo $count_todo_urgent; ?> task<?php echo $count_todo_urgent == 1 ? "" : "s"; ?> marked <strong>urgent</strong>
                <?php else : ?>
                    <?php echo Html::a($w->localUrl("/task/tasklist?assignee_id=" . $w->Auth->user()->id . "&task_priority=Urgent"), $count_todo_urgent . " task" . ($count_todo_urgent == 1 ? "" : "s")); ?> marked <strong>urgent</strong>
                <?php endif; ?>
            </h2>
        </div>
    </div>
</div>

<h3>Time log summary <span class="right"><small>Last 14 days</small> <?php echo $w->Task->getFormatPeriod($time_total_overall); ?></span></h3>
<div class='row-fluid'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach($time_entries as $date => $entry_struct) : ?>
            <h4 style='border-bottom: 1px solid #777;'><?php echo $date; ?><span class='right'><?php echo $w->Task->getFormatPeriod($entry_struct['total']); ?></span></h4>
            <table class='small-12'>
                <thead><tr><th width="10%">From</th><th width="10%">To</th><th width="70%">Description</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($entry_struct['entries'] as $time_entry) : ?>
                        <tr>
                            <td><?php echo formatDate($time_entry->dt_start, "H:i:s"); ?></td>
                            <td><?php echo formatDate($time_entry->dt_end, "H:i:s"); ?></td>
                            <td><?php echo @$time_entry->getComment()->comment; ?></td>
                            <td>
                                <?php echo Html::a('/task/edit/' . $time_entry->task_id . "#timelog", "View Time Log"); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else : ?>
        <h4>No time logs found</h4>
    <?php endif; ?>
</div>