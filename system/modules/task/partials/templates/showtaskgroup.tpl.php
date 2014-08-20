<?php if (!empty($taskgroup)) : ?>
    <table class='taskgroup_summary small-12'>
        <thead>
            <tr>
                <th colspan='2'>
                    <a  target="_blank" href="/task/tasklist/?task_group_id=<?php echo $taskgroup->id; ?>">
                        <?php echo $taskgroup->title; ?>
                    </a>
                    <?php if ($taskgroup->getCanICreate()) : ?>
                        <span style="float: right;"><a target="_blank" href="/task/edit/?gid=<?php echo $taskgroup->id; ?>">+</a></span>
                    <?php endif; ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($taskgroup->statuses)) : ?>
                <?php foreach ($taskgroup->statuses as $status => $val) : ?>
                <?php 
                    $task_count = 0;
                    if (!empty($taskgroup->tasks)) {
                        $task_count = count(array_filter($taskgroup->tasks, function ($var) use (&$val) {
                            return (strcasecmp($var->status, $val[0]) == 0);
                        }));
                    }
                    if ($task_count > 0) : ?>
                        <tr>
                            <td><?php echo $val[0]; ?></td>
                            <td><?php echo $task_count; ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>