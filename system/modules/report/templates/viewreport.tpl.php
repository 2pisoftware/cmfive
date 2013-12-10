<script type="text/javascript">
    var current_tab = 1;
    function switchTab(num){
        if (num == current_tab) return;
        $('#tab-'+current_tab).hide();
        $('#tab-link-'+current_tab).removeClass("active");
        $('#tab-'+num).show().addClass("active");
        $('#tab-link-'+num).addClass("active");
        current_tab = num;
    }
</script>
<div class="tabs">
	<div class="tab-head">
		<?php if (($w->Auth->user()->hasRole("report_editor")) || ($w->Auth->user()->hasRole("report_admin"))) { ?>
			<a id="tab-link-1" href="#" class="active"	onclick="switchTab(1);">Edit Report</a>
			<a id="tab-link-2" href="#"	onclick="switchTab(2);">Members</a>
			<a id="tab-link-3" href="#"	onclick="switchTab(3);">View Database</a>
		<?php } ?>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<?php echo $btnrun . "<p>" . $viewreport; ?>
			<p>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo Html::box("/report/addmembers/".$reportid," Add New Members ",true) ?>
			<p>
			<?php echo $viewmembers; ?>
		</div>
		<div id="tab-3" style="display: none;">
			<?php echo $dbform; ?>
			<p>
        </div>
	</div>
</div>

<script language="javascript">
<?php 
if ($_REQUEST['tab'] && (!empty($_REQUEST['tab']))) {
	echo "	switchTab(" . $_REQUEST['tab'] . ");";
}
?>

	$.ajaxSetup ({
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
