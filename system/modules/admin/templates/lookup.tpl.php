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
		<a id="tab-link-1" href="#" class="active"	onclick="switchTab(1);">Lookup List</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">New Item</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<form action="<?=$webroot."/admin/lookup"?>" method="POST">
				<fieldset style="margin-top: 10px;">
					<legend>Search Lookup Items</legend>
						<table cellpadding=2 cellspacing=2 border=0>
							<tr>
							<td align=right style="padding-left:20px;">Type</td>
							<td><?php echo $typelist; ?></td>
							<td align=left><input type="submit" value=" Search Lookup "/></td>
							</tr>
						</table>
				</fieldset>
			</form>
			<p>
			<?php echo $listitem; ?>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo $newitem; ?>
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
