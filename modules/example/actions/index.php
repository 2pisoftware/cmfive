<?php
/**
 * @hook example_add_row_action(array(<ExampleData> 'data', <String> 'actions')
 * @param Web $w
 */
function index_ALL(Web $w) {
	// adding data to the template context
	$w->ctx("message","Example Data List");
	// get the list of data objects
	$listdata = $w->Example->getAllData();
	echo 'hello world'; die;
	
	// prepare table data
	$t[]=array("Title", "Data", "Checkbox", "Actions"); // table header
	if (!empty($listdata)) {
		foreach ($listdata as $d) {
			$row = array();
			$row[] = $d->title;
			$row[] = $d->data;
			//$row[] = $d->check;
			
			// prepare action buttons for each row
			$actions = array();
			if ($d->canEdit($w->Auth->user())) {
				$actions[] = Html::box("/example/edit/".$d->id, "Edit", true);
			}
			if ($d->canDelete($w->Auth->user())) {
				$actions[] = Html::b("/example/delete/".$d->id, "Delete", "Really delete?");
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
