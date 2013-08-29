<?= Html::b("/admin-templates/edit","Add Template") ?>
<?php
$table_rows = array();

if (!empty($templates_list)) : ?>
	<table class="tablesorter">
		<thead>
			<tr>
				<th>Title</th><th>Module</th><th>Category</th>
				<th>Active?</th><th>Created</th><th>Modified</th><th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($templates_list as $t) : ?>
				<tr>
					<td><?php echo $t->title; ?></a></td>
					<td><?php echo $t->module; ?></td>
					<td><?php echo $t->category; ?></td>
					<td><?php echo $t->is_active ? "Active" : "Inactive"; ?></td>
					<td><?php echo Date("H:i d-m-Y", $t->dt_created); ?></td>
					<td><?php echo Date("H:i d-m-Y", $t->dt_modified); ?></td>
					<td>
						<?= Html::b("/admin-templates/edit/".$t->id,"Edit",false) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>	
<? else: ?>
<p>There are no templates.</p>
<? endif; ?>