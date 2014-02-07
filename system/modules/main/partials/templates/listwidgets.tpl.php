<?php 

echo Html::box("/main/addwidget/{$module}", "Add Widget", true); 

if (!empty($widgets)) : ?>
	<div class="widget_container">
		<?php for($i = 0; $i < count($widgets); $i++) : ?>
			<div class="widget_part<?php echo $i%$columns + 1; ?>">
				<div class="widget_button">
					<?php echo Html::box("/main/configwidget/{$module}/{$widgets[$i]->source_module}/{$widgets[$i]->widget_name}", "Config", false, false, null, null, "isbox", null, "widget_config"); ?>
					<?php echo Html::a("/main/removewidget/{$module}/{$widgets[$i]->source_module}/{$widgets[$i]->widget_name}", "X", "Remove Widget", "widget_remove"); ?>
				</div>
				<?php // echo $w->partial($widgets[$i]->widget_name, null, $widgets[$i]->source_module); ?>
				<?php if (!empty($widgets[$i]->widget_class)) $widgets[$i]->widget_class->display(); ?>
			</div>
		<?php endfor; ?>
	</div>
	<script type="text/javascript">
		$('[class^="widget_part"]').hover(
			function(){
				$(this).find(".widget_button:first").stop(true, true).fadeIn(250);
			}, 
			function(){
				$(this).find(".widget_button:first").stop(true, true).fadeOut(250);	
			}
		);

		$("#")
	</script>
<?php endif; ?>
