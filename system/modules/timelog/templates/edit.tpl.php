<form action='/timelog/edit/<?php echo !empty($timelog->id) ? $timelog->id : ''; ?><?php echo $redirect ? '?redirect=' . $redirect : ''; ?>' method='POST' name='timelog_edit_form' target='_self' id='timelog_edit_form' class=' small-12 columns'  >
	<div class="row-fluid clearfix small-12 multicolform">
		
		<div class="panel clearfix">
			<div class="small-12 columns section-header">
				<h4><?php echo (!empty($timelog->id)) ? "Update" : "Create"; ?> timelog</h4>
			</div>
			<ul class="small-block-grid-1 medium-block-grid-1 section-body">
				<li>
					<label class="small-12 columns">Assigned User
						<?php if ($w->Auth->user()->is_admin) {
							echo (new \Html\Form\Autocomplete([
								"id|name"	=> "user_id",
								"title"		=> empty($timelog->id) ? $w->Auth->user()->getFullName() : $w->Auth->getUser($timelog->user_id)->getFullName(),
								"value"		=> empty($timelog->id) ? $w->Auth->user()->id : $timelog->user_id
							]))->setOptions($w->Auth->getUsers());
						} else {
							echo (new \Html\Form\InputField\Hidden([
								"name"		=> "user_id",
								"value"		=> empty($timelog->id) ? $w->Auth->user()->id : $timelog->user_id
							]));
						} ?>
					</label>
				</li>
			</ul>
			<ul class="small-block-grid-1 medium-block-grid-2 section-body">
				<li>
					<label class="small-12 columns">Module
						<?php echo (new \Html\Form\Select([
							"id|name"			=> "object_class",
							"selected_option"	=> $timelog->object_class ? : $tracking_class,
							"options"			=> $select_indexes
						])); ?>
					</label>
				</li>
				<li>
					<label class="small-12 columns">Search
						<?php echo (new \Html\Form\Autocomplete([
							"id|name"		=> "search",
							"title"			=> !empty($object) ? $object->getSelectOptionTitle() : null,
							"value"			=> !empty($timelog->object_id) ? $timelog->object_id : $tracking_id
						]))->setOptions(!empty($timelog->object_class) || !empty($tracking_class) ? $w->Timelog->getObjects($timelog->object_class ? : $tracking_class) : ''); ?>
					</label>
				</li>
				<?php echo (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ? : $tracking_id])); ?>
			</ul>
			<ul class="small-block-grid-1 medium-block-grid-2 section-body">
				<li>
					<label class="small-12 columns">Date
						<?php echo (new \Html\Form\InputField\Date([
							"id|name"		=> "date_start",
							"value"			=> $timelog->getDateStart()
						])); ?>
					</label>
				</li>
				<li>
					<label class="small-12 columns">Time Started
						<?php echo(new \Html\Form\InputField([
							"id|name"		=> "time_start",
							"value"			=> $timelog->getTimeStart(),
							"pattern"		=> "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
							"placeholder"	=> "e.g. 11:30, 11:30am, 23:30, 11:30pm",
							"required"		=> "true"
						])); ?>
					</label>
				</li>
			</ul>
			<?php if (!$timelog->isRunning()) : ?>
				<ul class="small-block-grid-1 medium-block-grid-2 section-body">
					<li>
						<div class="row-fluid clearfix">
							<div class="small-2 medium-1 columns">
								<?php echo (new \Html\Form\InputField\Radio([
									"name"		=> "select_end_method",
									"value"		=> "time",
									"class"		=> "right",
									"style"		=> "margin-top: 20px;",
									"checked"	=> "true"
								])); ?>
							</div>
							<div class="small-10 medium-11 columns">
								<label>End time
									<?php echo(new \Html\Form\InputField([
										"id|name"		=> "time_end",
										"value"			=> $timelog->getTimeEnd(),
										"pattern"		=> "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
										"placeholder"	=> "e.g. 11:30, 11:30am, 23:30, 11:30pm",
										"required"		=> "true"
									])); ?>
								</label>
							</div>
						</div>
					</li>
					<li>
						<div class="row-fluid">
							<div class="small-2 medium-1 columns">
								<?php echo (new \Html\Form\InputField\Radio([
									"name"		=> "select_end_method",
									"value"		=> "hours",
									"class"		=> "right",
									"style"		=> "margin-top: 20px;"
								])); ?>
							</div>
							<div class="small-10 medium-11 columns">
								<label>Hours/mins worked
									<div class="row-fluid">
										<div class="small-12 medium-6 columns" style="padding: 0px;">
											<?php echo (new \Html\Form\InputField\Number([
												"id|name"		=> "hours_worked",
												"value"			=> $timelog->getHoursWorked(),
												"min"			=> 0,
												"max"			=> 23,
												"step"			=> 1,
												"placeholder"	=> "Hours: 0-23",
												"required"		=> "true",
												"disabled"		=> "true"
											])); ?>
										</div>
										<div class="small-12 medium-6 columns" style="padding: 0px;">
											<?php echo (new \Html\Form\InputField\Number([
												"id|name"		=> "minutes_worked",
												"value"			=> $timelog->getMinutesWorked(),
												"min"			=> 0,
												"max"			=> 59,
												"step"			=> 1,
												"placeholder"	=> "Mins: 0-59",
												"disabled"		=> "true"
											])); ?>
										</div>
									</div>
								</label>
							</div>
						</div>
					</li>
				</ul>
			<?php endif; ?>
			<ul class="small-block-grid-1 medium-block-grid-1 section-body">
				<li>
					<label class="small-12 columns">Description
						<?php echo (new \Html\Form\Textarea([
							"id|name"		=> "description",
							"value"			=> !empty($timelog->id) ? $timelog->getComment()->comment : null,
							"rows"			=> 8
						])); ?>	
					</label>
				</li>
			</ul>
			<?php if (!empty($form)) : ?>
				<?php foreach($form as $form_section_heading => $form_array) : ?>
					<?php foreach($form_array as $form_element_key => $form_elements) : ?>
						<?php foreach($form_elements as $form_element) : ?>
							<ul class="small-block-grid-1 medium-block-grid-1 section-body">
								<li>
									<label class="small-12 columns"><?php echo $form_element->label; ?>
										<?php echo $form_element; ?>
									</label>
								</li>
							</ul>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<ul class="small-block-grid-1 medium-block-grid-1 section-body">
				<li>
					<div class="small-12 columns">
						<button class="button small">Save</button>
					</div>
				</li>
			</ul>
		</div>
	</div>
</form>
<script type="text/javascript">
	// Input values are module, search and description
	$(document).ready(function () {
		$("input[type=radio][name=select_end_method]").change(function() {
			if (this.value === "time") {
				$("#time_end").removeAttr("disabled");
				
				$("#hours_worked").attr("disabled", "disabled");
				$("#minutes_worked").attr("disabled", "disabled");
				
				$("#hours_worked").val("");
				$("#minutes_worked").val("")
			} else if (this.value === "hours") {
				$("#hours_worked").removeAttr("disabled");
				$("#minutes_worked").removeAttr("disabled");
				
				$("#time_end").attr("disabled", "disabled");
				$("#time_end").val("");
			}
		});
		
		// If there is no task group selected, we disable submit
		if ($("#object_id").val() == '') {
			$(".savebutton").prop("disabled", true);
			$("#acp_search").attr("readonly", "true");
		}
		var searchBaseUrl = '/timelog/ajaxSearch';

		// If the start time changes and there is no end time then set end time
		// to start time, and vice versa
		$("#dt_start").focusout(function () {
			if ($("#dt_end").val() == "") {
				$('#dt_end').val($("#dt_start").val());
			}
			//console.log("Start has lost focus");
		});
		$("#dt_end").focusout(function () {
			if ($("#dt_start").val() == "") {
				$('#dt_start').val($("#dt_end").val());
			}
		});

		// If there is already a value in #object_class, that is, we are 
		// editing, then set the searchURL
		if ($("#object_class").val !== '') {
			searchUrl = searchBaseUrl + "?index=" + $(this).val();
		}
		$("#object_class").change(function () {
			$("#acp_search").val('');
			$("#timelog_edit_form .panel + .panel").remove();
			if ($(this).val() !== "") {
				$("#acp_search").removeAttr("readonly");
				searchUrl = searchBaseUrl + "?index=" + $(this).val();
			} else {
				// This fails with unknown page...
				$("#acp_search").attr("readonly", "true");
				searchUrl = searchBaseUrl;
			}
		});

		$("#acp_search").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: searchUrl + "&term=" + request.term,
					success: function (result) {
						response(JSON.parse(result));
					}
				});
			},
			// When the have selected a search value then do the ajax call  
			select: function (event, ui) {
				$("#object_id").val(ui.item.id);
				// Task is chosen, allow submit
				$(".savebutton").prop("disabled", false);
				$("#timelog_edit_form .panel + .panel").remove();
				$.get('/timelog/ajaxGetExtraData/' + $("#object_class").val() + '/' + $("#object_id").val())
						.done(function (response) {
							if (response != '') {
								var append_panel = "<div class='panel'><div class='row-fluid section-header'><h4>Additional Fields" + $("#object_class").val() + "</h4></div><ul class='small-block-grid-1 medium-block-grid-1 section-body'><li>" + response + "</li></ul></div>";
								$("#timelog_edit_form .panel").after(append_panel);
							}
						});

			},
			minLength: 3
		});

		$("#timelogForm").on("submit", function () {
			$.ajax({
				url: '/timelog/ajaxStart',
				method: 'POST',
				data: {
					'object': $("#object_class").val(),
					'object_id': $("#object_id").val(),
					'description': $("#description").val()
				},
				success: function (result) {
					alert(result);
				}
			});
			return false;
		});

		// Need to simulate change to module type to set url

	});

</script>