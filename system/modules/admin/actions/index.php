<?php
function index_ALL(Web &$w) {
	$w->Admin->navigation($w,"Dashboard");
	$w->ctx("currentUsers",$w->Admin->getLoggedInUsers());
        
        $w->ctx("printers", $w->Printer->getPrinters());
}
