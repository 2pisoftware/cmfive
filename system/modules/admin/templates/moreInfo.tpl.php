<div class="tabs">
    <div class="tab-head">
        <a id="tab-link-1" href="#1" onclick="switchTab(1);">Members</a>
    </div>
    <div class="tab-body">
<!--  Members Tab  -->
    	<div id="tab-1">
    		<div id="members_list" style="width:100%;">
    			<p><?php echo $addMember.$editPermission?></p>
    			<p><?php echo $memberList?></p>
    		</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	if(!window.location.hash)
		var current_tab = 1;
	else
	{
		var hash = window.location.hash;
		
		var current = hash.split("#");

		var current_tab = current[1];
	}
	
    $('#tab-'+current_tab).show().addClass("active");
    
    $('#tab-link-'+current_tab).addClass("active");
    
    function switchTab(num)
    {
        if (num == current_tab) return;
        
        $('#tab-'+current_tab).hide()
        $('#tab-link-'+current_tab).removeClass("active");
        $('#tab-'+num).show().addClass("active");
        $('#tab-link-'+num).addClass("active");
        
        current_tab = num;
    }
    
    switchTab(<?php echo $_REQUEST['tab'] ? $_REQUEST['tab'] : current_tab; ?>);
</script>