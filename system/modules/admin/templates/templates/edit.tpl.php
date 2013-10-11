<?php echo Html::b("/admin-templates","Back to Templates List",false); ?>
<br/><br/>
<div class="tabs">
	<div class="tab-head">
		<a id="tab-link-1" href="#" class="active" onclick="switchTab(1);">Details</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">Template</a>
		<a id="tab-link-3" href="#"	onclick="switchTab(3);">Test Data</a>
		<a id="tab-link-4" href="#"	onclick="switchTab(4);">Test Output</a>
		<a id="tab-link-5" href="#"	onclick="switchTab(5);">Manual</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<?php echo !empty($editdetailsform) ? $editdetailsform : '';?>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo !empty($templateform) ? $templateform : '';?>
		</div>
		<div id="tab-3" style="display: none;">
			<?php echo !empty($testdataform) ? $testdataform : '';?>
		</div>
		<div id="tab-4" style="display: none;">	
			<p><?php echo !empty($testtitle) ? $testtitle : '';?></p>
			<hr>
			<p><?php echo !empty($testbody) ? $testbody : '';?></p>
        </div>
        <div id="tab-5" style="display: none;">
			this is the template manual
        </div>
	</div>
</div>
