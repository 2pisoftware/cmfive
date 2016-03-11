<?php
/**
 * @hook example_add_row_action(array(<ExampleData> 'data', <String> 'actions')
 * @param Web $w
 */
function index_ALL(Web $w) {
	// adding data to the template context
	$w->ctx("message",__("Example Data List"));
	$listdata=$w->Example->getAllData();
	// prepare table data
	$t[]=array(__("Title"), __("Data"), __("Checkbox"), __("Actions")); // table header
	if (!empty($listdata)) {
		foreach ($listdata as $d) {
			$row = array();
			$row[] = $d->title;
			$row[] = $d->data;
			$row[] = $d->example_checkbox ? __("Yes") : __("No");
			
			// prepare action buttons for each row
			$actions = array();
			if ($d->canEdit($w->Auth->user())) {
				$actions[] = Html::abox("/example/edit/".$d->id, __("Edit"),'editbutton');
			}
			if ($d->canDelete($w->Auth->user())) {
				$actions[] = Html::ab("/example/delete/".$d->id, __("Delete"),'deletebutton', __("Really delete?"));
			}
			
			// allow any other module to add actions here
			//$actions = $w->callHook("example", "add_row_action", array("data" => $d, "actions" => $actions));
			
			$row[] = implode(" ",$actions);
			$t[] = $row;
		}
	}
	
	// create the html table and put into template context
	$w->ctx("table",Html::table($t,"table","tablesorter",true));
}
