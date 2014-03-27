<?php
function index_ALL(Web &$w) {
	AdminLib::navigation($w,"Dashboard");
	$w->ctx("currentUsers",$w->Admin->getLoggedInUsers());
        
        $w->ctx("printers", $w->Printer->getPrinters());
}
