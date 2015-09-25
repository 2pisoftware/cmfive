<div class="row-fluid panel">
	<?php echo $form->description; ?>
</div>

<div class="tabs">
	<div class="tab-head">
		<a href="#fields">Fields</a>
	</div>
	<div class="tab-body">
		<div id="fields">
			<?php echo Html::box("/form-field/edit/?form_id=" . $form->id, "Add a field", true); ?>
			
			<?php if (!empty($fields)) : ?>
				<table class="table small-12">
					<thead>
						<tr>
							<th>Name</th><th>Type</th><th>Additional Details</th><th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($fields as $field) : ?>
							<tr>
								<td><?php echo $field->name; ?></td>
								<td><?php echo $field->type; ?></td>
								<td><?php echo $field->mask; ?></td>
								<td>
									<?php echo Html::box("/form-fields/edit/" . $field->id, "Edit", true) ?>
									<?php echo Html::b("/form-fields/delete/" . $field->id, "Delete", "Are you sure you want to delete this form field? (WARNING: there may be existing data saved to this form field!)", null, false, "alert"); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>