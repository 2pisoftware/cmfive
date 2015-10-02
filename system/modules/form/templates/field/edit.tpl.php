<?php echo $form; ?>

<script>

	$("select[name='type']").change(function(event) {
		var _this = $(this);
		$.get("/form-field/ajaxGetMetadata/<?php echo $field->id; ?>?type=" + _this.val(), function(response){
			if (response.length) {
				_this.closest('.row-fluid').after(response);
			}
		});
	});

</script>