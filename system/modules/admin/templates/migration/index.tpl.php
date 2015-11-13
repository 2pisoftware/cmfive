<?php use Carbon\Carbon; ?>

<?php if (!empty($status)) : ?>
	<h4><?php echo $status; ?></h4>
<?php endif; ?>

<?php if (!empty($available)) : ?>
	<ul id="migrations_list" class="tabs vertical" style="border: 1px solid #444;" data-tab>
		<?php foreach($available as $module => $available_in_module): ?>
			<li class="tab-title">
				<a href="#<?php echo $module; ?>">
					<?php echo ucfirst($module); ?>
					<div class="right">
						<?php
							echo count(@$installed[$module]) > 0 ? "<span class='label round success' style='font-size: 14pt;'>" . count($installed[$module]) . "</span>" : "";
						
							echo (count($available_in_module) - count(@$installed[$module]) > 0 ? '<span class="label round warning" style="font-size: 14pt;">' . (count($available_in_module) - count(@$installed[$module])) . '</span>' : '');
						?>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="tabs-content">
		<?php foreach($available as $module => $available_in_module) : ?>
			<div class="content" style="padding-top: 0px;" id="<?php echo $module; ?>">
				<?php echo Html::box("/admin-migration/create/" . $module, "Create a" . (in_array($module{0}, ['a', 'e', 'i' ,'o', 'u']) ? 'n' : '') . ' ' . $module . " migration", true); ?>
				<?php if (count($available[$module]) > 0) : ?>
					<?php echo Html::b("/admin-migration/run/" . $module, "Run all " . $module . " migrations", "Are you sure you want to run all outstanding migrations for this module?"); ?>
					<table>
						<thead>
							<tr><th>Name</th><th>Path</th><th>Date run</th><th>Actions</th></tr>
						</thead>
						<tbody>
							<?php foreach($available_in_module as $a_migration_path => $a_migration_class): ?>
							<tr <?php echo ($w->Migration->isInstalled($a_migration_class)) ? 'style="background-color: #43CD80;"' : ''; ?>>
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
								<?php
									$filename = basename($a_migration_path, ".php");
									if ($w->Migration->isInstalled($a_migration_class)) {
										echo Html::b('/admin-migration/rollback/' . $module . '/' . $filename, "Rollback to here", "Are you 110% sure you want to rollback a migration? DATA COULD BE LOST PERMANENTLY!", null, false, "warning expand");
									} else {
										echo Html::b('/admin-migration/run/' . $module . '/' . $filename, "Migrate to here", "Are you sure you want to run a migration?", null, false, "info expand");
									}
								?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<script>
		$(document).ready(function() {
			$("ul#migrations_list li:visible").click(function() {
				window.location.hash = $('a', $(this)).attr('href');
			});
			
			var tab_item;
			if (window.location.hash) {
				$("ul#migrations_list li:visible").each(function(index, element) {
					if ($('a', element).attr('href') == window.location.hash) {
						$(element).addClass('active');
						tab_item = $(element);
					}
				});
			} else {
				tab_item = $("ul#migrations_list li:visible").first(); //.addClass("active");
				tab_item.addClass("active");
			}
			
			if (tab_item) {
				var element_href = tab_item.find('a').attr('href');
				$(".tabs-content #" + element_href.substring(1, element_href.length).toLowerCase()).addClass("active");
			}
		});
	</script>
<?php else: ?>
	<h4>There are no migrations on this project</h4>
<?php endif;
