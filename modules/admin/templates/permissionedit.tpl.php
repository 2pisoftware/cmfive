<div id="permissions_list" style="width:100%;"><?php echo $permission?></div>

<script type="text/javascript">
	$(".fieldtitle").width(300);

	$(".form-section").attr("width","");

	$("#goBack").click(function(){

		history.back();
	});
</script>

<script type="text/javascript">
	var maskedArray = <?=$groupRoles?>

	for(var i in maskedArray)
	{
		$("#check_" + maskedArray[i]).attr("disabled", "disabled");
	}
</script>