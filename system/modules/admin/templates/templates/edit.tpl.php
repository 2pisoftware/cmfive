<?php echo Html::b("/admin-templates","Back to Templates List",false); ?>
<br/><br/>
<div class="tabs">

	<div class="tab-head">
		<a href="#details">Details</a>
		<a href="#template">Template</a>
		<a href="#test">Test Data</a>
        <a href="#"	onclick="$.colorbox({iframe:true, width: '95%', height: '95%', href:'/admin-templates/rendertemplate/<?php echo !empty($id) ? $id : ""; ?>'});">Test Output</a>
		<a href="#manual">Manual</a>
	</div>
	<div class="tab-body clearfix">
            <div id="details"><p>
                    <?php echo !empty($editdetailsform) ? $editdetailsform : '';?>
            </div>
            <div id="template" style="display: none;"><p>
                    <?php echo !empty($templateform) ? $templateform : '';?>
            </div>
            <div id="test" style="display: none;"><p>
                    <?php echo !empty($testdataform) ? $testdataform : '';?>
            </div>
            <div id="manual" style="display: none;"><p>
                            this is the template manual
            </div>
	</div>
</div>

