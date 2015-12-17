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
	
	
	// prepare table data
	$t[]=array("Title", "Data", "Checkbox", "Actions"); // table header
	if (!empty($listdata)) {
		foreach ($listdata as $d) {
			$row = array();
			$row[] = $d->title;
			$row[] = $d->data;
			$row[] = $d->example_checkbox ? "Yes" : "No";
			
			// prepare action buttons for each row
			$actions = array();
			if ($d->canEdit($w->Auth->user())) {
				$actions[] = Html::abox("/example/edit/".$d->id, "Edit",'editbutton');
			}
			if ($d->canDelete($w->Auth->user())) {
				$actions[] = Html::ab("/example/delete/".$d->id, "Delete",'deletebutton', "Really delete?");
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
