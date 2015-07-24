<?php

function ajaxSearch_GET(Web $w) {
    $results = $w->Search->getResults($w->request("term"), $w->request("index"));
    $result_ids = [];
    $result_objects = [];

    // Flatten results
//    var_dump($results[0]);
    if (!empty($results[0])) {
        foreach($results[0] as $result) {
            if (empty($result_ids[$result['class_name']])) {
                $result_ids[$result['class_name']] = [];
            }
            
            $result_ids[$result['class_name']][] = $result['object_id'];
        }
        
        // Fetch all objects
        foreach($result_ids as $class => $ids) {
            if (class_exists($class)) {
                $inst_class = new $class($w);
                $query = $w->db->get($inst_class->getDbTableName())->where('id', $ids)->fetch_all();
                if (!empty($query)) {
                    $query_objects = $inst_class->getObjectsFromRows($class, $query);
                    foreach($query_objects as $query_object) {
                        if ($query_object->canList($w->Auth->user()) || $query_object->canView($w->Auth->user())) {
                            $autocomplete = new stdClass();
                            $autocomplete->label = $query_object->getSelectOptionTitle();
                            $autocomplete->value = $query_object->id; //getSelectOptionValue();
                            $result_objects[] = $autocomplete;
                        }
                    }
                }
            }
        }
    }
    
    echo json_encode($result_objects);
}