<?php if (!empty($messages)) : ?>

	<table class="tablesorter">
		<thead><tr><th>ID</th><th>Type</th><th>Has Been Processed</th><th>Actions</th></tr></thead>
		<tbody>
			<?php foreach($messages as $m) : ?>
				<tr>
					<td><?php echo $m->id; ?></td>
					<td><?php echo $m->message_type; ?></td>
					<td><?php echo $m->is_processed ? "Yes" : "No"; ?></td>
					<td></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php else: ?>

	<p>No messages found.</p>

<?php endif; ?>