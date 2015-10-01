<?php echo $form; ?>

<script>

	$("input[name='type']").change(function(event) {
		var _this = $(this);
		$.get("/form-field/ajaxGetMetadata/<?php echo $field->id; ?>?type=" + _this.val(), function(response){
			if (response.length) {
				_this.after(response);
			}
		});
	});

</script>