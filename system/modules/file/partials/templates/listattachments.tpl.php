<?php
    echo Html::box(WEBROOT."/file/attach/".get_class($object)."/{$object->id}/".str_replace("/", "+", $redirect), "Attach a File", true) . "<br/><br/>";
	$notImages = array();
	if (!empty($attachments)) {
		foreach ($attachments as $att) {
			if ($att->isImage()) { ?>
				<div class="iamge_attachment">
					<a target="_blank" href='/file/path/<?php echo $att->fullpath; ?>'><img src='/file/path/<?php echo $att->fullpath; ?>' height="200" width="200" /></a>
					<div class="image_attachment_text"><?php echo $att->title; ?></div>
					<?php if (!empty($att->description)) : ?><div class="image_attachment_text"><?php echo $att->description; ?></div><?php endif; ?>
					<?php echo Html::a(WEBROOT . "/file/atdel/" . $att->id . "/" . (str_replace("/", "+", $redirect)), "Delete", null, null, "Do you want to delete this attachment?"); ?>
				</div>
	  <?php } else {
				$notImages[] = $att;
			}
		}
	}
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
 