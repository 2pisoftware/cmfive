<div class="tabs">
    <div class="tab-head">
        <a href="#report"><?php echo !empty($report->id) ? "Edit" : "Create"; ?> Report</a>
        <?php if (!empty($report->id)) : ?>
            <a href="#templates">Templates</a>
            <a href="#members">Members</a>
        <?php endif; ?>
        <a href="#database">View Database</a>
    </div>	
    <div class="tab-body">
        <div id="report" class="clearfix">
            <?php echo $btnrun . $report_form; ?>
        </div>
        <?php if (!empty($report->id)) : ?>
            <div id="templates">
                <?php echo Html::box("/report-templates/edit/{$report->id}", "Add Template", true); ?>
                <?php echo !empty($templates_table) ? $templates_table : ""; ?>
            </div>
            <div id="members" style="display: none;" class="clearfix">
                <?php echo Html::box("/report/addmembers/" . $report->id, " Add New Members ", true) ?>
                <?php echo $viewmembers; ?>
            </div>
        <?php endif; ?>
        <div id="database" style="display: none;" class="clearfix">
            <?php echo $dbform; ?>
        </div>
    </div>
</div>

<script language="javascript">

    $.ajaxSetup({
        cache: false
    });

    var report_url = "/report/taskAjaxSelectbyTable?id=";
    $("#dbtables").change(function() {
    	var field = $("#dbtables option:selected").val();
        $.getJSON(
                report_url + field,
                function(result) {
                    $('#dbfields').html(result);
                }
        );
    }
    );

</script>