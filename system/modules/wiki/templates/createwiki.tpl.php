<?php
$lines = array(
    "Create Wiki" => array(
        array(array("Title","text","title","")),
        array(array("Public","checkbox","is_public",0))
    )
);

echo Html::multiColForm($lines,$w->localUrl("/wiki/createwiki"),"POST","Create");
