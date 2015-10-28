<?php use Carbon\Carbon; ?>

<?php if (!empty($status)) : ?>
	<h4><?php echo $status; ?></h4>
<?php endif; ?>

<?php if (!empty($available)) : ?>
<div class='row-fluid'>
	<table class='small-12 columns'>
		<thead>
			<tr><th>Name</th><th>Path</th><th>Date run</th><th>Actions</th></tr>
		</thead>
		<tbody>
			<?php foreach($available as $a_migration_path => $a_migration_class): ?>
			<tr style='background-color: <?php echo ($w->Migration->isInstalled($a_migration_class)) ? '#43CD80' : '#FA8072'; ?>;'>
				<td><?php echo $a_migration_class; ?></td>
				<td><?php echo $a_migration_path; ?></td>
				<td>
					<?php if ($w->Migration->isInstalled($a_migration_class)) :
						$installedMigration = $w->Migration->getMigrationByClassname($a_migration_class); ?>
						<span data-tooltip aria-haspopup="true" title="<?php echo @formatDate($installedMigration->dt_created, "d-M-Y \a\\t H:i"); ?>">
							Run <?php echo Carbon::createFromTimeStamp($installedMigration->dt_created)->diffForHumans(); ?> by <?php echo  $w->Auth->getUser($installedMigration->creator_id)->getContact()->getFullName(); ?>
						</span>
					<?php endif; ?> 
				</td>
				<td>
					Rollback to here / migrate etc
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php else: ?>
	<h4>There are no migrations on this project</h4>
<?php endif;
