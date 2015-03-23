<?php
function role_comment_allowed(Web $w,$path) {
    return $w->checkUrl($path, "admin", null, "commnet") || 
           $w->checkUrl($path, "admin", null, "deletecomment") ||
           $w->checkUrl($path, "admin", null, "ajaxSaveComment");
}
