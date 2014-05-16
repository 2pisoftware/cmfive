<?php

function printqueue_GET(Web $w) {
    $path = realpath(FILE_ROOT . "print");
    $exclude = array("THUMBS.db");
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    
    $table_data = array();
    $table_header = array("Name", "Size", "Actions");
    
    foreach($objects as $name => $object){
        $filename = $object->getFilename();
        // Ignore files starting with '.' and in exclude array
        if ($filename[0] === '.' || in_array($filename, $exclude)) {
            continue;
        }
        
        $table_data[] = array(
            Html::a("/uploads/print/" . $filename, $filename),
            // Function below in functions.php
            humanReadableBytes($object->getSize()),
            Html::box("/admin/printfile?filename=" . urlencode($name), "Print", true)
        );
    }

    $w->out(Html::table($table_data, null, "tablesorter", $table_header));
}