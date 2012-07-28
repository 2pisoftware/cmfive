<div class="tabs">
	<div class="tab-head">
		<a href="/report/index?tab=1">Report Dashboard</a>
		<a href="#" class="active">Execute Report</a>
	</div>
	<div class="tab-body">
		<div>
			<?php echo $btnedit . "<p>" . $report; ?>
			<p>
		</div>
	</div>
</div>

<script language="javascript">
	$(document).ready(function() {
		$("#format").val("html");
	});
</script>