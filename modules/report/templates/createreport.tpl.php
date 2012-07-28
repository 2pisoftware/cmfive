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
		<a id="tab-link-1" href="#" class="active"	onclick="switchTab(1);">Create Report</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">View Database</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			Please review the <b>Help</b> file for full instructions on the special syntax used to create reports.
			<p>
           <?php echo $createreport; ?>
        </div>
		<div id="tab-2" style="display: none;">
			<?php echo $dbform; ?>
			<p>
        </div>
   </div>
</div>

<script language="javascript">
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
