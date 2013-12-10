		   <form id="leadfilter" action="<?php echo $webroot."/report/index"; ?>" method="POST">
				<fieldset style="margin-top: 10px;">
					<legend>Search Reports</legend>
						<table cellpadding=2 cellspacing=2 border=0>
							<tr>
								<td align=right style="padding-left:20px;">Modules</td><td><?php echo $modules; ?></td>
								<td align=right style="padding-left:20px;">Category</td><td><?php echo $category; ?></td>
								<td align=right style="padding-left:20px;">Type</td><td><?php echo $type; ?></td>
								<td align=right><input type="submit" name="taskFilter" value=" Search Reports "/></td>
								<td align=left><button id="clrForm">Reset Filter</button></td>
							</tr>
						</table>
				</fieldset>
			</form>
		    <p>
			<?php echo $viewreports; ?>

<script language="javascript">
	var myFlag = true;
	var module_url = "/report/reportAjaxListModules";
	
	$(document).ready(function() {
		$.getJSON(
			module_url + $(this).val(),
			function(result) {
				$('#module').parent().html(result);
				$("select#module").val("<?php echo $reqModule; ?>");
				$("select[id='module']").trigger("change");
				}
			);
	});
	
	$.ajaxSetup ({
	    cache: false
		});

	$("#clrForm").click(function(e) {
		e.preventDefault();
		myFlag = false;
		$("select#module").val("");
		$("select[id='module']").trigger("change");
		}
	);
	
	var cat_url = "/report/reportAjaxModuletoCategory?id="; 
	$("select[id='module']").live("change",function() {
		$.getJSON(
			cat_url + $(this).val(),
			function(result) {
				$('#category').parent().html(result);
				if (myFlag)
					$("select#category").val("<?php echo $reqCategory; ?>");
				$("select[id='category']").trigger("change");
				}
			);
		}
	);

	var type_url = "/report/reportAjaxCategorytoType?id="; 
	$("select[id='category']").live("change",function() {
		$.getJSON(
			type_url + $(this).val() + "_" + $("select[id='module']").val(),
			function(result) {
				$('#type').parent().html(result);
				if (myFlag)
					$("select#type").val("<?php echo $reqType; ?>");
				}
			);
		}
	);

</script>
