<p>
<?php echo $grouptasks; ?>
<p>

<script language="javascript">

	$.ajaxSetup ({
	    cache: false
		});

	var task_url = "/task/taskAjaxSelectbyTaskGroup?id="; 
	$("select[id='task_group_id'] option").click(function() {
		$.getJSON(
			task_url + $(this).val(),
			function(result) {
				$('#task_type').parent().html(result[0]);
				$('#priority').parent().html(result[1]);
				$('#first_assignee_id').parent().html(result[2]);
				}
			);
		}
	);
</script>
