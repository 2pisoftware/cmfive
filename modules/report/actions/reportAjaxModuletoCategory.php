<?php
// Search Filter: selecting an Module will dynamically load the Category dropdown with available values
function reportAjaxModuletoCategory_ALL(Web $w) {
	$category = array();
	$module = $_REQUEST['id'];

	// organise criteria
	$who = $w->session('user_id');
	$where = ($_REQUEST['id'] != "") ? " and r.module = '" . $_REQUEST['id'] . "'" : "";

	// get report categories from available report list
	$reports = $w->Report->getReportsbyUserWhere($who, $where);
	if ($reports) {
		foreach ($reports as $report) {
			if (!array_key_exists($report->category, $category))
			$category[$report->category] = array($report->getCategoryTitle(),$report->category);
		}
	}
	if (!$category)
	$category = array(array("No Reports",""));

	// load Category dropdown and return
	$category = Html::select("category",$category);

	$w->setLayout(null);
	$w->out(json_encode($category));
}
