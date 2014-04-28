<?php
function role_file_upload_allowed(Web $w,$path) {
    $actions = "/file\/(index";
    $actions .= "|attach";

    $actions .= ")/";
    return preg_match($actions, $path);
}

function role_file_download_allowed(Web $w,$path) {
    $actions = "/file\/(index";
    $actions .= "|path";
    $actions .= "|atthumb";
    $actions .= "|atfile";
    $actions .= "|atdel";
    $actions .= "|printview";
    $actions .= ")/";
    return preg_match($actions, $path);
}

?>
