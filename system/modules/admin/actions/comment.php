<?php

function comment_GET(Web $w){
    $p = $w->pathMatch("comment_id", "tablename", "object_id");

    $comment_id = intval($p["comment_id"]);
    $comment = $comment_id > 0 ? $w->Comment->getComment($comment_id) : new Comment($w);
    if ($comment === null){
        $comment = new Comment($w);
    }
    
    $help =<<<EOF
//italics//
**bold**
    		
* bullet list
* second item
** subitem
    
# numbered list
# second item
## sub item
    
[[URL|linkname]]
    
== Large Heading
=== Medium Heading
==== Small Heading
    
Horizontal Line:
---
EOF;
    
    //call hook for notification select
    $get_recipients = $w->callHook('comment', 'get_notification_recipients_' . $p['tablename'],['object_id'=>$p['object_id']]);
    
    $form = array(
        array("Comment","section"),
        array("", "textarea", "comment", $comment->comment, 100, 15, false),
    	array("Help","section"),
    	array("", "textarea", "-help",$help, 100, 5, false),
        array("", "hidden", "redirect_url", $w->request("redirect_url"))
        
    );
    //add checkboxes to the form for each notification recipient    
    if (!empty($get_recipients)) {
        foreach($get_recipients as $recipients) {
            $form[] = array("Notifications","section");
            $form[] = array("", "hidden", "is_notifications", 1);
            foreach ($recipients as $user_id => $is_notify) {
                $user = $w->Auth->getUser($user_id);
                if (!empty($user)) {
                    $form[] = array($user->getFullName() . '    ', 'checkbox', 'recipient_' . $user->id, $is_notify);


                }
            }
        }
    }
    
    
    // return the comment for display and edit
    $w->setLayout(null);
    $w->out(Html::form($form, $w->localUrl("/admin/comment/{$comment_id}/{$p["tablename"]}/{$p["object_id"]}"), "POST", "Save"));
}

function comment_POST(Web $w){
    $p = $w->pathMatch("comment_id", "tablename","object_id");
    $comment_id = intval($p["comment_id"]);
    
    $comment = $w->Comment->getComment($comment_id);
    $is_new = false;
    if ($comment === null){
        $comment = new Comment($w);
        $is_new = true;
    }
    
    $comment->obj_table = $p["tablename"];
    $comment->obj_id = $p["object_id"];
    $comment->comment = strip_tags($w->request("comment"));
    $comment->insertOrUpdate();
    
    //handle notifications
    if($w->request("is_notifications")) {        
        $recipients = [];        
        foreach($_POST as $key=>$value) {
            $exp_key = explode('_',$key);
            if ($exp_key[0] == 'recipient') {
                $recipients[] = $exp_key[1];
            }            
        }        
        $results = $w->callHook('comment', 'send_notification_recipients_' . $p['tablename'],['object_id'=>$p['object_id'], 'recipients'=>$recipients, 'commentor_id'=>$w->auth->loggedIn(),'comment'=>$comment, 'is_new'=>$is_new]);
    
        
    }
    
    $redirectUrl = $w->request("redirect_url");

    if (!empty($redirectUrl)){
        $w->msg("Comment saved", urldecode($redirectUrl));
    } else {
        $w->msg("Comment saved", $w->localUrl($_SERVER["REQUEST_URI"]));
    }
}