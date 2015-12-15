<!--<style>
	.fade-in {
		display: none;
	}
</style>-->
<div class='row-fluid clearfix fade-in text-center panel' style='display: none; margin: 0 auto;'>
	<div class='small-12 columns'>
		<h3>Congratulations!</h3>
		<h4>Cmfive has been successfully installed</h4>
	</div>
</div>

<div class='row-fluid clearfix fade-in text-center' style='display: none;'>
	<div class='small-12 columns'>
		<h4>What's next?</h4>
		<div class='row-fluid'>
			<div class='small-12 medium-offset-2 large-offset-4 medium-4 large-2 columns'>
				<?php echo Html::b('/auth/login', 'Login', null, null, false, 'expand'); ?>
			</div>
			<div class='small-12 medium-4 large-2 columns end'>
				<?php echo Html::b('/system/docs/api', 'Read API Docs'); ?>
			</div>
		</div>
	</div>
</div>

<script>

	$(document).ready(function() {
		$(".fade-in").each(function(index, element) {
			$(this).delay(index * 300).fadeIn(500);
		});
	});

</script>