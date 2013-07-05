<?php
function auth_listener_POST_ACTION(&$w) {
    /*
     * Create a news item every time a new user gets created!
    */
    if ($w->currentModule() == "admin"
            && $w->currentAction() == "useradd"
            && $w->currentRequestMethod() == "POST"
            && $w->ctx("user")
            && !$w->ctx("error")) {
    	// do something interesting!
    }
}

?>
