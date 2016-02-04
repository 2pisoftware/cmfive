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
							<th>Name</th><th>Technical Name</th><th>Type</th><th>Additional Details</th><th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($fields as $field) : ?>
							<tr>
								<td><?php echo $field->name; ?></td>
								<td><?php echo $field->technical_name; ?></td>
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
					<div class="row-fluid clearfix">
						<div class="small-12 columns">
						<?php 
							$mappings = Config::get('form.mapping');
							if (!empty($mappings)) {
								foreach($mappings as $mapping) {
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
		<div id="row_template">
			<form action="/form/edit/<?php echo $form->id; ?>?redirect_url=<?php echo urlencode("/form/show/" . $form->id); ?>#row_template" method="POST">
				<div class="row-fluid clearfix">
					<div class="small-12 columns">
						<label>Header row template
							<textarea id="header_template" name="header_template" placeholder="Leave empty for default" rows="4"><?php echo $form->header_template; ?></textarea>
						</label>
					</div>
				</div>
				<br/>
				<div class="row-fluid clearfix">
					<div class="small-12 columns">
						<label>Item row template
							<textarea id="row_template" name="row_template" placeholder="Leave empty for default" rows="6"><?php echo $form->row_template; ?></textarea>
						</label>
					</div>
				</div>
				<br/>
				<div class="row-fluid clearfix">
					<div class="small-12 columns">
						<button type="submit">Save</button>
					</div>
				</div>
			</form>
		</div>
		<div id="summary_template">
			<form action="/form/edit/<?php echo $form->id; ?>?redirect_url=<?php echo urlencode("/form/show/" . $form->id); ?>#summary_template" method="POST">
				<div class="row-fluid clearfix">
					<div class="small-12 columns">
						<label>Header row template
							<textarea id="summary_template" name="summary_template" placeholder="Leave empty for default" rows="4"><?php echo $form->summary_template; ?></textarea>
						</label>
					</div>
				</div>
				<br/>
				<div class="row-fluid clearfix">
					<div class="small-12 columns">
						<button type="submit">Save</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>