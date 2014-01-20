Add a New Channel: <?php echo Html::select("add_channel", array(array("Email", "email"))); ?>

<?php if (!empty($channels)) : ?>

	<?php foreach($channels as $c) : ?>
		<?php echo $c->id; ?>

	<?php endforeach; ?>

<?php endif; ?>

<script type="text/javascript">
	
	jQuery("#add_channel").change(function(e) {
		if (e.target.selectedIndex > 0) {
			jQuery.colorbox({href: "/channels-" + jQuery(this).val() + "/edit"}); //window.location.href = "/channels-" + jQuery(this).val() + "/edit";
		}
	});

</script>