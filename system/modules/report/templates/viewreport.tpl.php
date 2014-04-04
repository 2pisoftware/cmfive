<div class="tabs">
    <div class="tab-head">
        <?php if (($w->Auth->user()->hasRole("report_editor")) || ($w->Auth->user()->hasRole("report_admin"))) { ?>
            <a href="#view-report" class="active">Edit Report</a>
            <a href="#members">Members</a>
            <a href="#view-database">View Database</a>
        <?php } ?>
    </div>
    <div class="tab-body">
        <div id="view-report">
            <?php echo $btnrun . "<p>" . $viewreport; ?>
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
