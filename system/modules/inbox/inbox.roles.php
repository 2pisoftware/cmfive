<?php 
function role_inbox_reader_allowed(Web $w, $path) {
    $actions = "/inbox\/(index";
    $actions .= "|view";
    $actions .= "|allread";
    $actions .= "|archive";
    $actions .= "|delete";
    $actions .= "|deleteforever";
    $actions .= "|showarchive";
    $actions .= "|trash";
    $actions .= "|read";
    $actions .= ")/";
    return preg_match($actions, $path);
}

function role_inbox_sender_allowed(Web $w, $path) {
	return preg_match("/inbox(-.*)?\//",$path);
}
?>