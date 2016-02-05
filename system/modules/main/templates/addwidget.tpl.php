<?php echo $widgetform; ?>

<script type="text/javascript">

	jQuery(function(){
		jQuery("#widget_name").parent("tr").hide();
	});

	jQuery("#source_module").change(function(e) {
		console.log(e);
		if (e.target.selectedIndex > 0) {
			jQuery.ajax({
				url: "/main/ajax_getwidgetnames?source="+jQuery(this).val(),
				type: "GET",
				success: function(data) {
					var parsed = JSON.parse(data);
					for(var i in parsed) {
						jQuery("#widget_name").append("<option value='" + parsed[i] + "'>" + parsed[i] + "</option>");
					}

					jQuery("#widget_name").parent("tr").fadeIn();
					$.fn.colorbox.resize({});
				}
			});
		}
	});

</script>