<?php if (!empty($taskgroups)) : ?>
	<?php foreach($taskgroups as $tg) : ?>
		<?php echo $tg->title; ?>
	<?php endforeach; ?>
<?php endif; ?>