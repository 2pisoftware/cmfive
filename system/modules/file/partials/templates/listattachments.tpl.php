<?php
    echo Html::box(WEBROOT."/file/attach/".get_class($object)."/{$object->id}/".(str_replace("/", "+", $redirect)), "Attach a File", true);
    $notImages = array();
    if (!empty($attachments)) : ?>
        <br/><br/>
        <ul class="clearing-thumbs small-block-grid-2 medium-block-grid-6 large-block-grid-9" data-clearing>
        <?php foreach ($attachments as $att) : ?>
            <?php if ($att->isImage()) : ?>
                <li><a class="th" href="/uploads/<?php echo $att->fullpath; ?>"><img data-caption="<?php echo $att->title; ?>" src="<?php echo $att->getThumbnailUrl(); ?>"></a></li>
            <?php else :
                $notImages[] = $att;
            endif;
        endforeach; ?>
        </ul>
    <?php endif;

    if (!empty($notImages)) : ?>
        <table class="tablesorter">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notImages as $att): ?>
                    <tr>
                        <td>
                            <a target="_blank" href="<?php echo WEBROOT; ?>/file/atfile/<?php echo $att->id; ?>/<?php echo $att->filename; ?>"><?php echo $att->filename; ?></a>
                        </td>
                        <td><?php echo $att->title; ?></td>
                        <td><?php echo $att->description; ?></td>                    
                        <td><?php echo Html::a(WEBROOT . "/file/atdel/" . $att->id . "/" . (str_replace("/", "+", $redirect)), "Delete", null, null, "Do you want to delete this attachment?"); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
