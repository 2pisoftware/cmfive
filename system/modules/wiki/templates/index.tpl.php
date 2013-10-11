<?php
if (!empty($wikis)) {
	$table[]=array(
		"Wiki Title",
		"Date Created",
		"Last Modified Date",
		"Modified By",
		"Last Page Modified");
	foreach($wikis as $wi) {
		$p = $wi->getPageById($wi->last_modified_page_id);
		$table[]=array(
		Html::a(WEBROOT."/wiki/view/".$wi->name."/HomePage","<b>".$wi->title."</b>"),
		formatDateTime(0 + $wi->dt_created), 
		formatDateTime(0 + $p->dt_modified), 
		$w->Auth->getUser($p->modifier_id)->getFullName(),
		Html::a(WEBROOT."/wiki/view/".$wi->name."/".$p->name,$p->name));
	}
	echo Html::table($table,"wikilist","tablesorter",true);
}
?>