<?php 

echo Html::box("/main/addwidget/{$module}", "Add Widget", true); 

if (!empty($widgets)) : ?>
	<div class="widget_container">
		<?php for($i = 0; $i < count($widgets); $i++) : ?>
			<div class="widget_part<?php echo $i%$columns + 1; ?>">
				<?php echo $w->partial($widgets[$i]->widget_name, null, $widgets[$i]->source_module); ?>
			</div>
		<?php endfor; ?>
	</div>
<?php endif; ?>
