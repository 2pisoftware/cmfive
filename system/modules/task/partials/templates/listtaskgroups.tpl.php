<?php if (!empty($taskgroups)) : ?>
	<div class="taskgroups_container">
            <ul class="small-block-grid-1 medium-block-grid-3 large-block-grid-4">
		<?php foreach($taskgroups as $tg) : ?>
                    <li><?php echo $w->partial("showtaskgroup", array("taskgroup" => $tg, "redirect" => (!empty($redirect) ? $redirect : "/")), "task"); ?></li>
		<?php endforeach; ?>
            </ul>
	</div>
<?php endif; ?>