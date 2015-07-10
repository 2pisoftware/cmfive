<?php if (!empty($taskgroups)) : ?>
	<div class="taskgroups_container">
            
		<?php foreach($taskgroups as $tg) : ?>
                    <?php if ($tg->canView($w->Auth->user())) : ?>
                        <div class="row-fluid"><div class="columns small-12"><?php echo $w->partial("showtaskgroup", array("taskgroup" => $tg, "redirect" => (!empty($redirect) ? $redirect : "/")), "task"); ?></div></div>
                    <?php endif; ?>
		<?php endforeach; ?>
            
	</div>
<?php endif; ?>
