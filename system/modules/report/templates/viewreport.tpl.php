<div class="tabs">
    <div class="tab-head">
        <a href="#report">Edit Report</a>
        <a href="#members">Members</a>
        <a href="#database">View Database</a>
    </div>	
	<div class="tab-body">
		<div id="report">
			<?php echo $btnrun . "<p>" . $viewreport; ?>
			<p>
		</div>
		<div id="members" style="display: none;">
			<?php echo Html::box("/report/addmembers/".$reportid," Add New Members ",true) ?>
			<p>
			<?php echo $viewmembers; ?>
		</div>
		<div id="database" style="display: none;">
			<?php echo $dbform; ?>
			<p>
        </div>
        <div id="members">
            <?php echo Html::box("/report/addmembers/" . $reportid, " Add New Members ", true) ?>
            <p>
                <?php echo $viewmembers; ?>
        </div>
        <div id="view-database">
            <?php echo $dbform; ?>
            <p>
        </div>
    </div>
</div>

<script language="javascript">

            $.ajaxSetup({
                cache: false
            });

            var report_url = "/report/taskAjaxSelectbyTable?id=";
            $("select[id='dbtables'] option").click(function() {
                $.getJSON(
                        report_url + $(this).val(),
                        function(result) {
                            $('#dbfields').html(result);
                        }
                );
            }
            );

</script>
