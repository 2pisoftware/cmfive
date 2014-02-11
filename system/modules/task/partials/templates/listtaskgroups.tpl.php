<?php if (!empty($taskgroups)) : ?>
	<div class="taskgroups_container">
		<?php foreach($taskgroups as $tg) : ?>
			<?php echo $w->partial("showtaskgroup", array("taskgroup" => $tg, "redirect" => (!empty($redirect) ? $redirect : "/")), "task"); ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>