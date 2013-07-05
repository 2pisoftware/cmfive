<?php
$lines[] = array("Create Wiki","section");
$lines[] = array("Title","text","title","");
$lines[] = array("Public","checkbox","is_public",0);

echo Html::form($lines,$w->localUrl("/wiki/createwiki"),"POST","Create");
?>
