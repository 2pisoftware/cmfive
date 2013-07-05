<div class="tabs">
	<div class="tab-head">
		<a href="/report/listfeed">Feed List</a>
		<a class=active href="/report/createfeed/">Create Feed</a>
	</div>
	<div class="tab-body">
		<div>
			<table cellpadding=2 cellspacing=2 border=0>
			<tr valign=top>
			<td>
			<?php  echo $createfeed; ?>
			<?php echo $feedurl; ?>
			</td>
			<td><span id="feedtext"><?php echo $feedtext; ?></span></td>
            </tr>
            </table>
		</div>
	</div>
</div>

<script language="javascript">
	$.ajaxSetup ({
	    cache: false
		});

	var feed_url = "/report/feedAjaxGetReportText?id="; 
	$("select[id='rid'] option").click(function() {
		$.getJSON(
			feed_url + $(this).val(),
			function(result) {
				$('#feedtext').html(result);
			}
			);
		}
	);
</script>

