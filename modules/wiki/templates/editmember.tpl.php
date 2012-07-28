<?php
	$lines[] = array("Wiki Member","section");
	$lines[] = array("User","autocomplete","user_id",$mem->user_id,$w->auth->getUsersForRole("wiki_user"));
	$lines[] = array("Role","select","role",$mem->role,array("reader","editor"));

	echo Html::form($lines,$w->localUrl("/wiki/editmember/".$wiki->id."/".$mem->id),"POST","Save");
?>
<script type="text/javascript">
function flow_acp_user_id() {};
</script>