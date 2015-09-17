<?php echo Html::box("/timelog/edit?class={$class}&id={$id}" . (!empty($redirect) ? "&redirect=$redirect" : ''), "Add new timelog", true); ?>
<h4 style="display: inline; padding: 0px 5px;" class="right">
	<?php echo $w->Task->getFormatPeriod($total); ?>
</h4>

<?php if (!empty($timelogs)) : ?>
	<table class='small-12'>
		<thead><tr><th width="10%">From</th><th width="10%">To</th><th width="60%">Description</th><th width="20%">Actions</th></tr></thead>
		<tbody>
			<?php foreach($timelogs as $timelog) : ?>
				<tr>
					<td><?php echo formatDate($timelog->dt_start, "H:i:s"); ?></td>
					<td><?php echo formatDate($timelog->dt_end, "H:i:s"); ?></td>
					<td><?php echo $timelog->getComment()->comment; ?></td>
					<td>
						<?php echo Html::box('/timelog/edit/' . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Edit', true); ?>
						<?php echo Html::b('/timelog/delete/' . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Delete', 'Are you sure you want to delete this timelog?'); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;
