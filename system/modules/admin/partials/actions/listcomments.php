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
        $unique_recipients = [];
        foreach($get_recipients as $recipients) {
            foreach ($recipients as $user_id => $is_notify) {
                if($is_notify && !array_key_exists($user_id, $unique_recipients)){
                    $unique_recipients[$user_id] = $is_notify;
                }
            }
        }
        $recipients_form_html .= '<h4>Notifications</h4><input type="hidden" name="is_notifications" value="1" id="is_notifications"><div id="notifications_list">';
        $parts = array_chunk($unique_recipients, 4, true);
        foreach ($parts as $key=>$row) {
            
            $recipients_form_html .= '<ul class="small-block-grid-1 medium-block-grid-' . count($row) . ' section-body">';
            foreach ($row as $user_id => $is_notify) {
                $user = $w->Auth->getUser($user_id);
                if (!empty($user)) {
                    $recipients_form_html .= '<li><label class="small-12 columns">' . $user->getFullName() . ' <input type="checkbox" name="recipient_' . $user->id . '" value="1" checked="checked" id="recipient_' . $user_id . '" class=""></label></li>';                    
                }
            }
            $recipients_form_html .= '</ul>';
        }
        $recipients_form_html .= '</div>';        
    }
    $w->ctx('recipients_html', $recipients_form_html);
    
    //$w->ctx('recipients', json_encode($w->callHook('comment', 'get_notification_recipients_' . $object->getDbTableName(),['object_id'=>$object->id])));
}

