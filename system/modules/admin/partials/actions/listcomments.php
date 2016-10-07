<?php namespace System\Modules\Admin;

function listcomments(\Web $w, $params) {
    $object = $params['object'];
    $redirect = $params['redirect'];
    $w->ctx("comments", $w->Comment->getCommentsForTable($object->getDbTableName(), $object->id));
    $w->ctx("redirect", $redirect);
    $w->ctx("object", $object);
    
    //get recipients for comment notifications
    $get_recipients = $w->callHook('comment', 'get_notification_recipients_' . $object->getDbTableName(),['object_id'=>$object->id]);
    //add checkboxes to the form for each notification recipient    
    $recipients_form_html = '';
    if (!empty($get_recipients)) {
        
        foreach($get_recipients as $recipients) {
            $recipients_form_html .= '<h4>Notifications</h4>';
            $recipients_form_html .= '<input type="hidden" name="is_notifications" value="1" id="is_notifications">';
            $recipients_form_html .= '<div id="notifications_list">';
            foreach ($recipients as $user_id => $is_notify) {
                $user = $w->Auth->getUser($user_id);
                if (!empty($user)) {
                    $recipients_form_html .= '<label class="small-12 columns">';
                    $recipients_form_html .= $user->getFullName(); 
                    $recipients_form_html .= ' <input type="checkbox" name="recipient_' . $user->id;
                    $recipients_form_html .= '" value="1" checked="checked" id="recipient_' . $user_id;
                    $recipients_form_html .= '" class=""></label>';


                }
            }
            $recipients_form_html .= '</div>';
        }
        
    }
    $w->ctx('recipients_html', $recipients_form_html);
    
    //$w->ctx('recipients', json_encode($w->callHook('comment', 'get_notification_recipients_' . $object->getDbTableName(),['object_id'=>$object->id])));
}

