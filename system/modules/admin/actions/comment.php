<?php

function comment_GET(Web &$w){
    $p = $w->pathMatch("comment_id", "tablename", "object_id");

    $comment_id = intval($p["comment_id"]);
    $comment = $comment_id > 0 ? $w->Comment->getComment($comment_id) : new Comment($w);
    if ($comment === null){
        $comment = new Comment($w);
    }
    
    $form = array(
        array("Comment","section"),
        array("", "textarea", "comment", $comment->comment, 100, 15, true),
        array("", "hidden", "redirect_url", urlencode($w->request("redirect_url")))
    );

    // return the comment for display and edit
    $w->setLayout(null);
    $w->out(Html::form($form, $w->localUrl("/admin/comment/{$p["comment_id"]}/{$p["tablename"]}/{$p["object_id"]}"), "POST", "Save"));
}

function comment_POST(Web &$w){
    $p = $w->pathMatch("comment_id", "tablename","object_id");
    $comment_id = intval($p["comment_id"]);
    
    $comment = $comment_id > 0 ? $w->Comment->getComment($comment_id) : new Comment($w);
    if ($comment === null){
        $comment = new Comment($w);
    }
    
    $comment->obj_table = $p["tablename"];
    $comment->obj_id = $p["object_id"];
    $comment->comment = strip_tags($w->request("comment"));
    $comment->insertOrUpdate();
    
    $redirectUrl = $w->request("redirect_url");

    if (!empty($redirectUrl)){
        $w->msg("Comment saved", urldecode($redirectUrl));
    } else {
        $w->msg("Comment saved", $w->localUrl($_SERVER["REQUEST_URI"]));
    }
}