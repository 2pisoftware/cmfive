<div class="tabs">
	<div class="tab-head">
		<a href="#members">Members</a>
		<a href="#notifications">Notifications</a>
	</div>
	<div class="tab-body">
		<div id="members">
            <?php echo Html::b("/task/tasklist/?task_group_id={$groupid}", "Task List"); ?>
            <?php echo Html::box("/task-group/addgroupmembers/".$grpid," Add New Members ",true); ?>
            <?php echo Html::box($webroot."/task-group/viewtaskgroup/".$groupid," Edit Task Group ", true); ?>
            <?php echo Html::box($webroot."/task-group/deletetaskgroup/".$groupid," Delete Task Group ", true); ?>
            <?php echo $viewmembers; ?>
		</div>
		<div id="notifications">
			<?php echo !empty($notifymatrix) ? $notifymatrix : ""; ?>
		</div>
	</div>
</div>
