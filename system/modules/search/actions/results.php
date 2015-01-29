<?php
function results_GET(Web $w) {
	$response = array("success" => true, "data" => "");
        $w->setLayout(null);
	$q = $w->request('q'); // query
	$idx = $w->request('idx'); // index
	$p = $w->request('p'); // page
	$ps = $w->request('ps'); // pageSize	
	$tr = $w->request('tr'); // total results
	
	if ($q && strlen($q) >= 3) {
            $results = $w->Search->getResults($q, $idx,$p,$ps);

            if (empty($p) && empty($ps) && empty($tr)) {
                $buffer = "";
                if (!empty($results[0])) {
                    
                    // Group results by class_name
                    $filter_results = array();
                    foreach($results[0] as $res) {
                    	$searchobject = $w->Search->getObject($res['class_name'], $res['object_id']);
                    	if (!empty($searchobject)) {
                        	$filter_results[$res['class_name']][] = $searchobject;
                    	} 
                    }
                    
                    foreach($filter_results as $class => $objects) {
                        // Transform class into readable text
                        $t_class = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $class);
                        $buffer .= "<div class='row search-class'><h4 style='padding-top: 10px; font-weight: lighter;'>{$t_class}</h4>";
                        
                        if (!empty($objects)) {
                            foreach($objects as $object) {
                                if ($object->canList($w->Auth->user())) {
                                    $buffer .= '<div class="panel search-result">';
                                    if ($object->canView($w->Auth->user())) {
                                        $buffer .= "<a class=\"row search-title\" href=\"".$w->localUrl($object->printSearchUrl())."\">{$object->printSearchTitle()}</a>"
                                        . "<div class=\"row search-listing\">{$object->printSearchListing()}</div>";
                                    } else {
                                        $buffer .= "<div class=\"small-12 columns search-title\">{$object->printSearchTitle()}</div><div class=\"row search-listing\">(restricted)</div>";
                                    }
                                    $buffer .= "</div>";
                                }
                            }
                        }
                        
                        $buffer .= "</div>";
                    }
                }
                $response["data"] = $buffer;
            }
	} else {
            $response["success"] = false;
            $response["data"] = "Please enter at least 3 characters for searching.";
	}
        
        echo json_encode($response);
}
