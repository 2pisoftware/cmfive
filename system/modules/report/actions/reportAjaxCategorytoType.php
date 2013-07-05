<?php
// Search Filter: selecting an Category will dynamically load the Type dropdown with available values
function reportAjaxCategorytoType_ALL(Web $w) {
	$type = array();

	list($category, $module) = preg_split('/_/',$_REQUEST['id']);

	// organise criteria
	$who = $w->session('user_id');
	$where = ($module != "") ? " and r.module = '" . $module . "'" : "";
	$where .= ($category != "") ? " and r.category = '" . $category . "'" : "";

	// get report categories from available report list
	$reports = $w->Report->getReportsbyUserWhere($who, $where);
	if ($reports) {
		foreach ($reports as $report) {
			$arrtype = preg_split("/,/", $report->sqltype);
			foreach ($arrtype as $rtype) {
				$rtype = trim($rtype);
				if (!array_key_exists(strtolower($rtype), $type))
				$type[strtolower($rtype)] = array(strtolower($rtype),strtolower($rtype));
			}
		}
	}
	if (!$type)
	$type = array(array("No Reports",""));

	// load Type dropdown and return
	$type = Html::select("type",$type);

	$w->setLayout(null);
	$w->out(json_encode($type));
}