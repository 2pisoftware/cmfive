            <?php
                // Shows standard comment box, url is in the form:
                // /admin/comment/[COMMENT_ID]/[TABLE_NAME]/[OBJECT_ID]?redirect_url=[REDIRECT_URL]
                // Its a bit farfetched but provides us with a standard commenting interface
                // Dont need to worry about urlencoding the redirect url
                echo Html::box("/admin/comment//{$object->getDbTablename()}/{$object->id}?redirect_url=" . 
                    $redirect, "Add Comment", true); ?>
            <?php if (!empty($comments)) : ?>
                <br/><br/>
                <?php foreach($comments as $c) : ?>
                    <?php echo $c; ?><br/><br/>
                    <?php 
                        // To edit comment, just add the ID
                        echo Html::box("/admin/comment/{$c->id}/{$c->obj_table}/{$c->obj_id}?redirect_url=" . 
                                $redirect, "Edit", true); ?><br/><br/>
                <?php endforeach; ?>
            <?php endif; ?>
