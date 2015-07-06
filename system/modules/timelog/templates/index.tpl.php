<div class='row-fluid'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach($time_entries as $date => $entry_struct) : ?>
            <h4 style='border-bottom: 1px solid #777;'><?php echo $date; ?><span class='right'><?php echo $w->Task->getFormatPeriod($entry_struct['total']); ?></span></h4>
            <table class='small-12'>
                <thead><tr><th width="10%">From</th><th width="10%">To</th><th width="20%">Object</th><th width="50%">Description</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($entry_struct['entries'] as $time_entry) : ?>
                        <tr>
                            <td><?php echo formatDate($time_entry->dt_start, "H:i:s"); ?></td>
                            <td><?php echo formatDate($time_entry->dt_end, "H:i:s"); ?></td>
                            <td><?php echo ($time_entry->getLinkedObject() ? $time_entry->getLinkedObject()->toLink() : ''); ?></td>
                            <td><?php echo $time_entry->getComment()->comment; ?></td>
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
<?php echo !empty($pagination) ? $pagination : ''; ?>