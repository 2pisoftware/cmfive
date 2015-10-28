<?php use Carbon\Carbon; ?>

<?php if (!empty($status)) : ?>
	<h4><?php echo $status; ?></h4>
<?php endif; ?>

<?php if (!empty($available)) : ?>
	<ul id="migrations_list" class="tabs vertical" style="border: 1px solid #444;" data-tab>
		<?php foreach($w->modules() as $module): ?>
			<li class="tab-title" <?php echo count($available[$module]) == 0 ? 'style="display: none;"' : ''; ?>><a href="#<?php echo $module; ?>"><?php echo $module; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tabs-content">
		<?php foreach($available as $module => $available_in_module) : ?>
			<div class="content" style="padding-top: 0px;" id="<?php echo $module; ?>">
				<table>
					<thead>
						<tr><th>Name</th><th>Path</th><th>Date run</th><th>Actions</th></tr>
					</thead>
					<tbody>
						<?php foreach($available_in_module as $a_migration_path => $a_migration_class): ?>
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
		<?php endforeach; ?>
	</div>
	<script>
		$(document).ready(function() {
			var tab_item = $("ul#migrations_list li:visible").first(); //.addClass("active");
			tab_item.addClass("active");
			
			$(".tabs-content #" + tab_item.find('a').text()).addClass("active");
		});
	</script>
<?php else: ?>
	<h4>There are no migrations on this project</h4>
<?php endif;
