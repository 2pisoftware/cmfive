<?php if (!empty($taskgroup)) : ?>
	<table class='taskgroup_summary'>
		<thead>
			<tr>
				<th colspan='2'>
					<?php echo $taskgroup->title; ?>
					<?php if ($taskgroup->getCanICreate()) : ?>
						<span style="float: right;"><a target="_blank" href="/task/createtask/?gid=<?php echo $taskgroup->id; ?>">+</a></span>
					<?php endif; ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($taskgroup->statuses as $status => $val) : ?>
				<tr>
					<td><?php echo $val[0]; ?></td>
					<td>
						<?php
							if (!empty($taskgroup->tasks)) {
								echo count(array_filter($taskgroup->tasks, function ($var) use (&$val) {
									return (strcasecmp($var->status, $val[0]) == 0); 
								}));
							} else { echo 0; }
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>