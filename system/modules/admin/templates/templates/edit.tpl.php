<?php echo Html::b("/admin-templates","Back to Templates List",false); ?>
<br/><br/>
<div class="tabs">
	<div class="tab-head">
		<a id="tab-link-1" href="#" class="active" onclick="switchTab(1);">Template</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">Test Data</a>
		<a id="tab-link-3" href="#"	onclick="switchTab(3);">Test Output</a>
		<a id="tab-link-3" href="#"	onclick="switchTab(4);">Manual</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
		<?php if (!empty($template)) : ?>
			<?php echo $editform;?>
		<?php else : ?>
			<p>Template not found.</p>
		<?php endif; ?>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo $testdataform;?>
		</div>
		<div id="tab-3" style="display: none;">
			<?php echo $testoutput;?>
        </div>
        <div id="tab-4" style="display: none;">
			this is the template manual
        </div>
	</div>
</div>

