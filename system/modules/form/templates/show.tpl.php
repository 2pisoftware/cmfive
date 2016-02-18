<div class="row-fluid panel">
	<?php echo $form->description; ?>
</div>

<div class="tabs">
	<div class="tab-head">
		<a href="#fields">Fields</a>
		<a href="#preview">Preview</a>
		<a href="#mapping">Mapping</a>
		<a href="#row_template">Row Templates</a>
		<a href="#summary_template">Summary Template</a>
	</div>
	<div class="tab-body">
		<div id="fields">
			<?php echo Html::box("/form-field/edit/?form_id=" . $form->id, "Add a field", true); ?>

			<?php if (!empty($fields)) : ?>
				<table class="table small-12">
					<thead>
						<tr>
							<th width="5%">Ordering</th><th>Name</th><th>Technical Name</th><th>Type</th><th>Additional Details</th><th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($fields as $field) : ?>
							<tr id="field_<?php echo $field->id; ?>" >
								<td><i class="draggable-icon fi-list large"></i></td>
								<td><?php echo $field->name; ?></td>
								<td><?php echo $field->technical_name; ?></td>
								<td><?php echo $field->type; ?></td>
								<td><?php echo $field->getAdditionalDetails(); ?></td>
								<td>
									<?php
									echo Html::box("/form-field/edit/" . $field->id . "?form_id=" . $form->id, "Edit", true);
									echo Html::b("/form-field/delete/" . $field->id, "Delete", "Are you sure you want to delete this form field? (WARNING: there may be existing data saved to this form field!)", null, false, "alert");
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<script>
					
					// Code from: http://www.html5rocks.com/en/tutorials/dnd/basics/
					
					(function() {
						var dragSrcEl_ = null;
						var rows = document.querySelectorAll('#fields tbody tr');
						
						this.handleDragStart = function (e) {
							e.dataTransfer.effectAllowed = 'move';
							e.dataTransfer.setData('text/html', this.outerHTML);

							dragSrcEl_ = this;

							// this/e.target is the source node.
//							$(this).addClass('moving');
						};

						this.handleDragOver = function (e) {
							if (e.preventDefault) {
								e.preventDefault(); // Allows us to drop.
							}

							e.dataTransfer.dropEffect = 'move';

							return false;
						};

						this.handleDragEnter = function (e) {
//							$(this).addClass('over');
						};

						this.handleDragLeave = function (e) {
							// this/e.target is previous target element.
//							$(this).removeClass('over');
						};

						this.handleDrop = function (e) {
							// this/e.target is current target element.

							if (e.stopPropagation) {
								e.stopPropagation(); // stops the browser from redirecting.
							}

							// Don't do anything if we're dropping on the same column we're dragging.
							if (dragSrcEl_ != this) {
								dragSrcEl_.outerHTML = this.outerHTML;
								this.outerHTML = e.dataTransfer.getData('text/html');
							}

							return false;
						};

						this.handleDragEnd = function (e) {
							// Get new ordering and update via ajax
							var ordering = [];
							// var rows = document.querySelectorAll("#fields tbody tr");
							
							$("#fields tbody tr").each(function(index, element) {
								var id_split = $(element).attr("id").split("_");
								var id = id_split[1];
								
								ordering.push(id);
							});
							
							$.post("/form-field/move/<?php echo $form->id; ?>", {ordering: ordering}, function() {
								// Rebinding the events doesn't work...
								window.location.reload();
							});
						};
						
						for(var i = 0; i < rows.length; i++) {
							rows[i].setAttribute('draggable', 'true');  // Enable columns to be draggable.
							rows[i].addEventListener('dragstart', this.handleDragStart, false);
							rows[i].addEventListener('dragenter', this.handleDragEnter, false);
							rows[i].addEventListener('dragover', this.handleDragOver, false);
							rows[i].addEventListener('dragleave', this.handleDragLeave, false);
							rows[i].addEventListener('drop', this.handleDrop, false);
							rows[i].addEventListener('dragend', this.handleDragEnd, false);
						}
					})();
					
	//				function allowDrop(ev) {
	//					ev.preventDefault();
	//				}
	//
	//				var dragSrcEl = null;
	//
	//				function drag(e) {
	//					// Target (this) element is the source node.
	//					e.dataTransfer.effectAllowed = 'move';
	//					e.dataTransfer.setData('text/html', this.innerHTML);
	//				}
	//
	//				function drop(ev) {
	//					ev.preventDefault();
	//					var data = ev.dataTransfer.getData("text/html");
	//					
	//					var element = ev.target;
	//					var foundTbody = (element.nodeName.toLowerCase() === 'tbody');
	//					
	//					// Find tbody parent
	//					if (!foundTbody) {
	//						while(element && element.parentNode && !foundTbody) {
	//							console.log(element.nodeName);
	//							element = element.parentNode;
	//							if (element.nodeName.toLowerCase() === 'tbody') {
	//								foundTbody = true;
	//							}
	//						}
	//					}
	//					
	//					if (foundTbody) {
	//						element.appendChild(document.getElementById(data));
	//					}
	//				}
				</script>
			<?php endif; ?>
		</div>
		<div id="preview">
			<div class="row-fluid clearfix">
				<?php echo Html::multiColForm($w->Form->buildForm(new FormInstance($w), $form), "/form/show/" . $form->id . "?preview=1"); ?>
			</div>
		</div>
		<div id="mapping">
			<div class="row-fluid clearfix">
				<form action="/form-mapping/edit/?form_id=<?php echo $form->id; ?>" method="POST">
					<div class="row-fluid clearfix">
						<div class="small-12 columns">
							<?php
							$mappings = Config::get('form.mapping');
							if (!empty($mappings)) {
								foreach ($mappings as $mapping) {
									echo Html::checkbox($mapping, $w->Form->isFormMappedToObject($form, $mapping));
									echo "<label>$mapping</label>";
								}
							}
							?>
						</div>
					</div>
					<div class="row-fluid clearfix">
						<div class="small-12 columns">
							<button class="button">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div id="row_template" class="clearfix">
			<?php
			echo Html::multiColForm([
				"Row templates" => [
					[["Header row template", "textarea", "header_template", $form->header_template, null, "4", "codemirror"]],
					[["Item row template", "textarea", "row_template", $form->row_template, null, "6", "codemirror"]]
				]
					], "/form/edit/" . $form->id . "?redirect_url=" . urlencode("/form/show/" . $form->id) . "#row_template", "POST");
			?>
		</div>
		<div id="summary_template" class="clearfix">
			<?php
			echo Html::multiColForm([
				"Summary template" => [
					[["", "textarea", "summary_template", $form->summary_template, null, "4", "codemirror"]],
				]
					], "/form/edit/" . $form->id . "?redirect_url=" . urlencode("/form/show/" . $form->id) . "#summary_template", "POST");
			?>
		</div>
	</div>
</div>