<?php
function role_file_upload_allowed(Web $w,$path) {
    return $w->checkUrl($path, "file", null, "index") || 
           $w->checkUrl($path, "file", null, "attach");
}

function role_file_download_allowed(Web $w,$path) {
    return $w->checkUrl($path, "file", null, "index") ||
           $w->checkUrl($path, "file", null, "path") ||
           $w->checkUrl($path, "file", null, "atthumb") ||
           $w->checkUrl($path, "file", null, "atdel") ||
           $w->checkUrl($path, "file", null, "printview") ||
           $w->checkUrl($path, "file", null, "atfile") ||
           $w->checkUrl($path, "file", null, "thumb");
}
