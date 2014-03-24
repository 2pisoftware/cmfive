<?php
function lookup_ALL(Web &$w) {
	$w->Admin->navigation($w,"Lookup");

	$types = $w->Admin->getLookupTypes();

	$typelist = Html::select("type",$types, $w->request('type'));
	$w->ctx("typelist",$typelist);

	// tab: Lookup List
	$where = array();
	if ($w->request('type') != "") {
		$where['type'] = $_REQUEST['type'];
	}
	$lookup = $w->Admin->getAllLookup($where);

	$line[] = array("Type","Code","Title","");

	if ($lookup) {
		foreach ($lookup as $look) {
			$line[] = array(
			$look->type,
			$look->code,
			$look->title,
			Html::box($w->localUrl("/admin/editlookup/".$look->id."/".urlencode($_REQUEST['type']))," Edit ",true) .
						"&nbsp;&nbsp;&nbsp;" .
			Html::b($webroot."/admin/deletelookup/".$look->id."/".urlencode($_REQUEST['type'])," Delete ", "Are you sure you wish to DELETE this Lookup item?")
			);
		}
	}
	else {
		$line[] = array("No Lookup items to list");
	}

	// display list of items, if any
	$w->ctx("listitem",Html::table($line,null,"tablesorter",true));


	// tab: new lookup item
	$types = $w->Admin->getLookupTypes();

	$f = Html::form(array(
	array("Create a New Entry","section"),
	array("Type","select","type", null,$types),
	array("or Add New Type","text","ntype"),
	array("Key","text","code"),
	array("Value","text","title"),
	),$w->localUrl("/admin/newlookup/"),"POST"," Save ");
	 
	$w->ctx("newitem",$f);
}
