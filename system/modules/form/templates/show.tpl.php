<div class="row-fluid panel">
	<?php echo $form->description; ?>
</div>

<div class="tabs">
	<div class="tab-head">
		<a href="#fields">Fields</a>
		<a href="#preview">Preview</a>
		<a href="#mapping">Mapping</a>
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
								<td><?php echo $field->getAdditionalDetails(); ?></td>
								<td>
									<?php echo Html::box("/form-field/edit/" . $field->id . "?form_id=" . $form->id, "Edit", true) ?>
									<?php echo Html::b("/form-field/delete/" . $field->id, "Delete", "Are you sure you want to delete this form field? (WARNING: there may be existing data saved to this form field!)", null, false, "alert"); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
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
					<?php foreach($w->modules() as $module) : ?>
						<div class="small-12 medium-2 columns">
							<label><?php echo ucfirst($module); ?></label>
							<?php echo Html::checkbox($module, $w->Form->isFormMappedToObject($form, $module)); ?>
						</div>
					<?php endforeach; ?>
					
					<button class="button">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>