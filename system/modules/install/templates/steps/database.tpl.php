<style>
	button.cancelbutton {
		display: none;
	}
</style>

<div class="row">
	<?php echo $form_details; ?>
</div>

<div style='display: none;' class='output row panel' id='output'>
	<h4>Output from import</h4>
</div>

<script>
	
	jQuery(document).ready(function() {
		jQuery("#install_form").submit(function() {
			var data = $(this).serialize();

			// Check the connect with ajax
			if (!confirm("This action will empty the " + $("#install_form #db_database").val() + " database, are you sure you wish to proceed?")) {
				return false;
			}
			
			$(".savebutton").remove();
			toggleModalLoading();
			jQuery.ajax('/install-steps/import?' + data, {
				
			}).done(function(response) {
				jQuery("#output").append(response).show();
				toggleModalLoading();
				jQuery("#output").append("<a id='finish_button' class='button success' href='/install-steps/finish'>Finish</a>");
				jQuery('html,body').animate({
					scrollTop: $("#finish_button").offset().top
				}, 2000);
			});

			return false;
		});
	});
	
</script>
	