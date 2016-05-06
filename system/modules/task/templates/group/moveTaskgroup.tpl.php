<div class="row-fluid clearfix">
	<div class="small-12 medium-6 large-4 columns">
		<label>Select new Taskgroup
			<?php echo Html::autocomplete("taskgroup", $w->Task->getTaskGroups()); ?>
		</label>
	</div>
</div>
<div class="row-fluid clearfix" id="taskgroup_identical" style="display: none;">
	<div class="small-12 columns">
		<hr/>
		<p>No additional details are needed</p>
		<form action="/task-group/saveNewTaskgroup" method="POST">
			<?php echo (new \Html\Form\InputField\Hidden([
				"name" => "old_taskgroup_id",
				"id" => "old_taskgroup_id",
				"value" => $old_taskgroup->id
			]));
			echo (new \Html\Form\InputField\Hidden([
				"name" => "new_taskgroup_id",
				"id" => "new_taskgroup_id"
			]));
			echo (new \Html\Form\InputField\Hidden([
				"name" => "task_id",
				"id" => "task_id",
				"value" => $task->id
			]));
			?>
			<button class="button">Save</button>
		</form>
	</div>
</div>
<div id="taskgroup_results" style="display: none;">
	<hr/>
	<form action="/task-group/saveNewTaskgroup" method="POST">
		<div class="row-fluid clearfix">
			<div class="small-12 medium-6 large-4 columns">
				<label>Select new Task Type (was <?php echo $task->task_type; ?>)
					<div id="new_task_type_placeholder">
						<?php echo (new \Html\Form\Select())->setName("new_task_type")->setId("new_task_type"); ?>
					</div>
				</label>
			</div>
		</div>
		<div class="row-fluid clearfix">
			<div class="small-12 medium-6 large-4 columns">
				<label>Select new Status (was <?php echo $task->status; ?>)
					<div id="new_status_placeholder">
						<?php echo (new \Html\Form\Select())->setName("new_status")->setId("new_status"); ?>
					</div>
				</label>
			</div>
		</div>
		<div class="row-fluid clearfix">
			<div class="small-12 medium-6 large-4 columns">
				<label>Select new Priority (was <?php echo $task->priority; ?>)
					<div id="new_priority_placeholder">
						<?php echo (new \Html\Form\Select())->setName("new_priority")->setId("new_priority"); ?>
					</div>
				</label>
			</div>
		</div>
		<div class="row-fluid clearfix">
			<div class="small-12 medium-6 large-4 columns">
				<label>Select new Assignee (was <?php echo $w->Auth->getUser($task->assignee_id)->getFullName(); ?>)
					<div id="new_assignee_placeholder">
						<?php echo (new \Html\Form\Select())->setName("new_assignee")->setId("new_assignee"); ?>
					</div>
				</label>
			</div>
		</div>
		<div class="row-fluid clearfix">
			<div class="small-12 medium-6 large-4 columns">
				<button class="button">Save</button>
			</div>
		</div>
	</form>
</div>
<script>

	var old_taskgroup_type = "<?php echo $old_taskgroup->task_group_type; ?>";

	function selectAutocompleteCallback(event, ui) {
		if (event.target.id == "acp_taskgroup") {
			$.get("/task-group/ajax_getTaskgroupDetails/" + ui.item.id, function(response) {
				var res = JSON.parse(response);
				debugger;
				if (res.taskgroup_type == old_taskgroup_type) {
					$("#new_taskgroup_id").val(ui.item.id);
					$("#taskgroup_identical").show();
				} else {
					// New taskgroup type, prompt for details
					$("#new_task_type_placeholder").html(res.task_types);
					$("#taskgroup_results").show();
				}
			});
		}
	}

</script>