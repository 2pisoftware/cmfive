<?php
    // Shows standard comment box, url is in the form:
    // /admin/comment/[COMMENT_ID]/[TABLE_NAME]/[OBJECT_ID]?redirect_url=[REDIRECT_URL]
    // Its a bit farfetched but provides us with a standard commenting interface
    // Dont need to worry about urlencoding the redirect url
    echo Html::box("/admin/comment//{$object->getDbTablename()}/{$object->id}?redirect_url=" . urlencode($redirect), "Add Comment", true);
    if (!empty($comments)) : ?>
        <div class="comment_container">
            <?php foreach($comments as $c) : ?>
                <div class="comment_section">
                    <div class="comment_body"><?php echo $c->comment; ?></div>
                    <div class="comment_meta">
                        Posted <?php echo !empty($c->dt_created) ? "on " . formatDate($c->dt_created, "d M \a\\t H:i") : ""; ?><b><?php echo !empty($c->creator_id) ? " by " . @$c->w->Auth->getUser($c->creator_id)->getFullName() : ""; ?></b>
                        <?php if ($c->w->Auth->user()->id == $c->creator_id) {
                            echo " - " . Html::box("/admin/comment/{$c->id}/{$c->obj_table}/{$c->obj_id}?redirect_url=" . $redirect, "Edit", false); 
                            echo " or ";
                            echo Html::a("/admin/deletecomment/{$c->id}?redirect_url=" . $redirect, "Delete", null, null, "Are you sure you want to delete this comment?"); 
                        } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
