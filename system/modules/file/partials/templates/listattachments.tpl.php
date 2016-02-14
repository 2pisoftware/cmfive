<style>
	
	div.image-container {
		width: auto;
		height: 220px;
		position: relative;
		margin-left: auto;
		margin-right: auto;
		overflow: hidden;
		padding: 10px;
		border: 1px solid #444;
		margin: 5px;
		background-color: #efefef;
	}

	div.image-container-overlay {
		background-color: rgba(0, 0, 0, 0.7);
		position: absolute;
		margin: auto;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 100;
		display: none;
		padding: 10px;
	}
	
	div.image-container-overlay > .row-fluid {
		height: 75px;
	}
 	
	div.image-container-overlay:hover {
		display: block;
	} 
	 
	img.image-cropped {
		position: absolute;
		margin: auto;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}
	
</style>
<div class="enable_drop_attachments" data-object="<?php echo get_class($object); ?>" data-id="<?php echo $object->id; ?>" style="display:none;"></div>

<?php
	if ($w->Auth->user()->hasRole("file_upload")) {
		echo Html::box("/file/new/" . get_class($object) . "/{$object->id}?redirect_url=" . urlencode($redirect), "Attach a File", true);
	}
	
    $notImages = array();
    if (!empty($attachments)) : ?>
        <br/><br/>
        <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
        <?php foreach ($attachments as $attachment) : ?>
            <?php if ($attachment->isImage()) : ?>
				<li>
					<div class="image-container attachment">
						<div class="image-container-overlay">
							<div class="row-fluid">
								<button href="#" data-reveal-id="attachment_modal_<?php echo $attachment->id; ?>" class="button expand">View</button>
							</div>
							<div class="row-fluid">
								<?php echo Html::box("/file/edit/" . $attachment->id . "?redirect_url=" . urlencode($redirect), "Edit", true, null, null, null, null, null, "button expand secondary"); ?>
							</div>
							<div class="row-fluid">
								<?php echo Html::b("/file/delete/" . $attachment->id . "?redirect_url=" . urlencode($redirect), "Delete", "Are you sure you want to delete this attachment?", null, false, "expand alert ");?>
							</div>
						</div>
						<img class="image-cropped" data-caption="<?php echo $attachment->title; ?>" src="<?php echo $attachment->getThumbnailUrl(); ?>">
					</div>
					
					<a href="#" data-reveal-id="attachment_modal_<?php echo $attachment->id; ?>">
						<div class="row-fluid clearfix text-center">
							<b><?php echo $attachment->title; ?></b>
						</div>
						<div class="row-fluid clearfix text-center">
							<?php echo strip_tags($attachment->description); ?>
						</div>
					</a>
					<div id="attachment_modal_<?php echo $attachment->id; ?>" class="reveal-modal" data-reveal role="dialog">
						<div class="row-fluid panel" style="text-align: center;">
							<img src="/file/atfile/<?php echo $attachment->id; ?>" alt="<?php echo $attachment->title; ?>" />
						</div>
						
						<h2 id="firstModalTitle" style="font-weight: lighter; text-align: center; border-bottom: 1px solid #777;"><?php echo $attachment->title; ?></h2>
						<p style="text-align: center;"><?php echo $attachment->description; ?></p>
						
						<a href="/file/atfile/<?php echo $attachment->id; ?>" target="_blank" class="button expand secondary" onclick="$('#attachment_modal_<?php echo $attachment->id; ?>').foundation('reveal', 'close');">Open in new tab/window</a>
						
						<a class="close-reveal-modal" aria-label="Close">&#215;</a>
					</div>
				</li>
            <?php else :
                $notImages[] = $attachment;
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
                    <tr class="attachment">
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

	<script>
		
		$(document).ready(function() {
			$(".image-container-overlay button").removeClass("tiny");
			$(".image-container").hover(function() {
				$(".image-container-overlay", this).stop().fadeIn("fast");
			}, function() {
				$(".image-container-overlay", this).stop().fadeOut("fast");
			});
		});

	</script>