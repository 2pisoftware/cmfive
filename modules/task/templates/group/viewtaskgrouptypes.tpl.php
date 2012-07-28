<?php

?>

<!-- 
<style>
.dvhdr1 {
	background: #BD7;
	font-family: arial;
	font-size: 12px;
	font-weight: bold;
	border: 1px solid #9C5;
	padding: 5px;
	width: 150px;
}

.dvbdy1 {
	background: #FFF;
	font-family: arial;
	font-size: 12px;
	border-left: 1px solid #C8BA92;
	border-right: 1px solid #C8BA92;
	border-bottom: 1px solid #C8BA92;
	padding: 5px;
	width: 150px;
}

.loading {
	color: #2A4480;
	border-top: thin black solid;
	border-bottom: thin black solid;
	padding: 1em;
}

#errorReport {
	border: grey medium solid;
	margin-left: 20px;
	margin-bottom: 5px;
	padding-left: 10px;
	padding-top: 10px;
	padding-bottom: 10px;
}

#errorDoubleReport {
	border: grey medium solid;
	margin-left: 20px;
	padding-left: 10px;
	padding-top: 10px;
	padding-bottom: 10px;
	margin-bottom: 5px;
}
</style>
-->

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
		<a id="tab-link-1" href="#" class="active"	onclick="switchTab(1);">Task Groups</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">New Task Group</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<?php echo $dashboard; ?>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo $creategroup; ?>
		</div>
	</div>
</div>
