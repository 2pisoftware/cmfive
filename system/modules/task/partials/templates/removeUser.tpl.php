
<?php if (!empty($tasks) || !empty($task_group_membership)) : ?>
	<h4 class="text-center">Task</h4>

	<form action="/task-user/unassign/<?php echo $user->id; ?>?redirect=<?php echo urlencode($redirect); ?>" method="POST">
		<?php if (!empty($tasks)) : ?>
			<p>This user has <strong><?php echo count($tasks); ?></strong> task<?php echo (count($tasks) == 1 ? '' : 's'); ?> assigned to them.</p>
		<?php endif; ?>
		<?php if (!empty($task_group_membership)) : ?>
			<p>This user is a member of <strong><?php echo count($task_group_membership); ?></strong> task group<?php echo (count($task_group_membership) == 1 ? '' : 's'); ?>
			<?php if ($default_taskgroup_assignee > 0): ?> 
				and they are the default assignee for <strong><?php echo $default_taskgroup_assignee; ?></strong> of these
			<?php endif; ?>
			</p>
		<?php endif; ?>

		<p>The following actions will be carried out:</p>
		<ul>
			<li>Remove user membership from any taskgroups</li>
			<li>Remove user from any taskgroup default assignee (WARNING: result will be that taskgroup has no default assignee)</li>
			<li>Reassign any tasks assigned to the user to the default user (As with above, if the user is assigned and also the default assignee, the result will be tasks become unassigned)</li>
		</ul>

		<button class="button warning expand" onclick="toggleModalLoading();" type="submit">Remove from Task</button>
	</form>

<?php endif;
	