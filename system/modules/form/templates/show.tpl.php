<div class="row-fluid panel">
	<?php echo $form->description; ?>
</div>

<div class="tabs">
	<div class="tab-head">
		<a href="#fields">Fields</a>
	</div>
	<div class="tab-body">
		<div id="fields">
			<?php echo Html::box("/form-field/edit/", "Add a field"); ?>
			
			<?php if (!empty($fields)) : ?>
				<table class="table small-12">
					<thead>
						<tr>
							<th>Name</th><th>Type</th><th>Mask</th><th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($fields as $field) : ?>
							<tr>
								<td><?php echo $field->name; ?></td>
								<td><?php echo $field->type; ?></td>
								<td></td>
								<td>
									
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>