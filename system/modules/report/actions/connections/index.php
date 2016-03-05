<?php

function index_ALL(Web $w) {
   	echo "PM:". t($w,'permodule');
	echo "PS:". t($w,'persub');
	echo "PA:".t($w,'peraction');
	echo "PSA:".t($w,'persubaction');
//	echo $w->Translation->translate('name');
	echo "asdfasdf";
	die();

   
    $connections = $w->Report->getConnections();
    
    $table_header = array("Driver", "Host", "Database", "Port", "Username", "Actions");
    $table_body = array();
    if (!empty($connections)) {
        foreach($connections as $conn) {
            $conn->decrypt();
            $table_body[] = array(
                $conn->db_driver, $conn->db_host, $conn->db_database, $conn->db_port, $conn->s_db_user,
                Html::box("/report-connections/test/{$conn->id}", "Test Connection", true) .
                Html::box("/report-connections/edit/{$conn->id}", "Edit", true) .
                Html::b("/report-connections/delete/{$conn->id}", "Delete", "Are you sure you want to remove this connection?")
            );
        }
    }
    
    $w->ctx("connections_table", Html::table($table_body, null, "tablesorter", $table_header));
}
