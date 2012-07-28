<?php

?>

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
		<a href="/task/index">Task Dashboard</a>
		<a href="/task/tasklist/?taskgroups=<?php echo $groupid; ?>">Task List</a>
		<a id="tab-link-1" href="#" class="active"	onclick="switchTab(1);">Members</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">Notifications</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<p>
			&nbsp;&nbsp;
			<?= Html::box($webroot."/task-group/deletetaskgroup/".$groupid," Delete Task Group ", true) ?>
			&nbsp;&nbsp;
			<?= Html::box($webroot."/task-group/viewtaskgroup/".$groupid," Edit Task Group ", true) ?>
			&nbsp;&nbsp;
			<?= Html::box("/task-group/addgroupmembers/".$grpid," Add New Members ",true) ?>
			<p>
			<?php echo $viewmembers; ?>
		</div>
		<div id="tab-2" style="display:none;">
			<?php  echo $notifymatrix; ?>
		</div>
	</div>
</div>

<script language="javascript">
<?php 
if ($_REQUEST['tab'] && (!empty($_REQUEST['tab']))) {
	echo "	switchTab(" . $_REQUEST['tab'] . ");";
}
?>
</script>
