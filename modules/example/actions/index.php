<?php
/*
 * @author Carsten Eckelmann, carsten@2pisystems.com, 2014
 */
function index_ALL(Web $w) {
	// adding data to the template context
	$w->ctx("message","Example Data List");
	
	// get the list of data objects
	$listdata = $w->Example->getAllData();
	
	// prepare table data
	$t[]=array("Title", "Data", "Actions");
	if (!empty($listdata)) {
		foreach ($listdata as $d) {
			$row = array();
			$row[] = $d->title;
			$row[] = $d->data;
			
			$actions = "";
			if ($d->canEdit($w->Auth->user())) {
				$actions .= " ".Html::box("/example/edit/".$d->id, "Edit", true);
			}
			if ($d->canDelete($w->Auth->user())) {
				$actions .= " ".Html::b("/example/delete/".$d->id, "Delete", "Really delete?");
			}
			$row[] = $actions;
			
			$t[] = $row;
		}
	}
	
	// create the html table and put into template context
	$w->ctx("table",Html::table($t,"table","tablesorter",true));
}
