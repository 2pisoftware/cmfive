<?php if (!empty($attachments)) : ?>
	
	<?php foreach($attachments as $attachment_adapter => $sorted_attachments) : ?>
		<h3>Files on <?php echo $attachment_adapter; ?></h3>
		<table class="small-12 columns">
			<thead>
				<tr><th>Title</th><th>Location</th><th>Actions</th></tr>
			</thead>
			<tbody>
				<?php foreach($sorted_attachments as $attachment) : ?>
					<tr>
						<td><?php echo $attachment->title; ?></td>
						<td><?php echo $attachment->adapter; ?></td>
						<td>
							<button href="#" data-dropdown="move_to_<?php echo $attachment->id; ?>" aria-controls="move_to_<?php echo $attachment->id; ?>" aria-expanded="false" class="button dropdown expand">Move to</button><br>
							<ul id="move_to_<?php echo $attachment->id; ?>" data-dropdown-content class="f-dropdown" aria-hidden="true">
								<?php if (!empty($adapters)) : ?>
									<?php foreach($adapters as $adapter) : ?>
										<?php if ($adapter != $attachment_adapter) : ?>
											<li><a id="<?php echo $attachment->id; ?>" href="#"><?php echo $adapter; ?></a></li>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach;
endif; ?>

<script>
	
	$(".f-dropdown a").click(function() {
		window.location.href="/admin-file/index/" + $(this).attr("id") + "?adapter=" + $(this).html();
	});
	
</script>