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
		<a href="/task/tasklist">Task List</a>
		<a id="tab-link-1" href="#" onclick="switchTab(1);" class="active">Task Details</a>
		<a id="tab-link-2" href="#" onclick="switchTab(2);">Time Log</a>
		<a id="tab-link-3" href="#"	onclick="switchTab(3);">Task Comments (<?= $numComments ?>)</a>
		<a id="tab-link-4" href="#" onclick="switchTab(4);">Task Documents (<?= $numDocos ?>)</a>
		<?= $tasknotifications ?>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<?php echo $btndelete; ?>&nbsp;&nbsp;&nbsp;<?php echo $btntimelog; ?>
			<table cellspacing="10">
			<tr><td valign="top"><?php echo $viewtask; ?></td>
			<td valign="top"><?php echo $extradetails; ?></td></tr>
			</table>
			<p></p>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo $addtime ?>
			<?php echo $timelog ?>
		</div>
		<div id="tab-3" style="display: none;">
			<?= $addComment ?>
			<?= $comments ?>
		</div>
		<div id="tab-4" style="display: none;">
			<?php echo $btnAttachment ?>&nbsp;&nbsp;&nbsp;<?php echo $btnPage ?>
			<?php echo $docos ?>
		</div>
		<?= $tasknotify ?>
	</div>
</div>

<script language="javascript">
<?php 
if ($_REQUEST['tab'] && (!empty($_REQUEST['tab']))) {
	echo "	switchTab(" . $_REQUEST['tab'] . ");";
}
?>

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
