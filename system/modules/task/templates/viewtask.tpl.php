<div class="tabs">
	<div class="tab-head">
		<a id="tab-link-1" href="#" onclick="switchTab(1);" class="active">Task Details</a>
		<a id="tab-link-2" href="#" onclick="switchTab(2);">Time Log</a>
		<a id="tab-link-3" href="#"	onclick="switchTab(3);">Task Comments</a>
		<a id="tab-link-4" href="#" onclick="switchTab(4);">Task Documents</a>
		<?php echo !empty($tasknotifications) ? $tasknotifications : null; ?>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<?php echo !empty($btndelete) ? $btndelete : null; ?>
			&nbsp;&nbsp;&nbsp;<?php echo !empty($btntimelog) ? $btntimelog : null; ?>
			&nbsp;&nbsp;&nbsp;<?php echo $task->getTaskTypeObject()->displayExtraButtons($task);?>
			<table cellspacing="10">
			<tr><td valign="top"><?php echo !empty($viewtask) ? $viewtask : null; ?></td>
			<td valign="top"><?php echo !empty($extradetails) ? $extradetails : null; ?></td></tr>
			</table>
			<p></p>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo !empty($addtime) ? $addtime : null; ; ?>
			<?php echo !empty($timelog) ? $timelog : null; ; ?>
		</div>
		<div id="tab-3" style="display: none;">
			 <?php echo $w->partial("listcomments",array("object"=>$task,"redirect"=>"task/viewtask/{$task->id}?tab=3"),"admin");?>
		</div>
		<div id="tab-4" style="display: none;">
			 <?php echo $w->partial("listattachments",array("object"=>$task,"redirect"=>"task/viewtask/{$task->id}?tab=4"),"file");?>
		</div>
		<?php echo !empty($tasknotify) ? $tasknotify : null; ?>
	</div>
</div>

<script language="javascript">

	$(".startTime").click(function(e){
    	var url = $(this).attr("href");
    	var screenW = screen.width;
    	var x = screenW - 360;
    	var t = 0; 	
        var winName = "Task Time Log";
    	var winParameters = "width=360,height=300,scrollbars=no,toolbar=no,status=no,menubar=no,location=no";

    	var thiscookie = getCookie("thiswin");
    	
		if (!thiscookie) {
	    	thiswin = window.open(url, winName, winParameters);
			thiswin.moveTo(x,t);
			thiswin.focus();
		}
		else {
			alert("Please END TIME on your current Task" + "\n" +  "before starting a new Task Time Log");

			if (typeof(thiswin) != "undefined" && !thiswin.closed)
				thiswin.focus();
		}

        e.preventDefault();
     });

function getCookie(cname) {
	var cVal = null;
	if(document.cookie) {
		var arr = document.cookie.split((escape(cname) + '=')); 
		if(arr.length >= 2) {
			var arr2 = arr[1].split(';');
			cVal  = unescape(arr2[0]);
       	    }
	    }
	    return cVal;
    }
</script>
