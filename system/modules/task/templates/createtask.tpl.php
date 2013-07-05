<?php
?>

<div class="tabs">
	<div class="tab-head">
		<a href="/task/index">Task Dashboard</a>
		<a href="/task/tasklist">Task List</a>
		<a id="tab-link-1" href="#" class="active">Create Task</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			Creating a new Task is a two step process.<br>Please complete all steps to register a Task.
			<p>
			<table border=0>
			<tr valign=top>
			<td><?php echo $createtask; ?></td>
			<td><span id="tasktext"><?php echo $tasktext; ?></span></td>
			</tr>
			</table>
		</div>
	</div>
</div>


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
				$('#tasktext').html(result[3]);
			}
			);
		}
	);
</script>

