<?php

function view_GET(Web &$w) {
    $pm = $w->pathMatch("wikiname", "pagename");
    
    // Check for missing parameter
    if (empty($pm["wikiname"])) {
        $w->error("Wiki does not exist.", "/wiki");
    }
    
    // Get wiki object and check for existance
    $wiki = $w->Wiki->getWikiByName($pm['wikiname']);
    if (empty($wiki->id)) {
        $w->error("Wiki does not exist.");
    }

    // If page doesn't exist, make one
    $wp = $wiki->getPage($pm['pagename']);
    if (!$wp) {
        $wp = $wiki->addPage($pm['pagename'], "New Page.");
    }
    
    // Reset wiki breadcrumbs
    if ($pm['pagename'] == "HomePage") {
        $_SESSION['wikicrumbs'][$pm['wikiname']] = array();
    } else {
        $_SESSION['wikicrumbs'][$pm['wikiname']][$pm['pagename']] = 1;
    }

    // Set navigation
    $w->Wiki->navigation($w, $wiki, $pm["pagename"]);
    
    // Set edt wiki form
    $editForm = array(
        "Edit" => array(
            array(array("Page", "textarea", "body", $wp->body, 60, 24, false))
        )
    );
    
    // Set template vars
    $w->ctx("body", WikiLib::wiki_format_creole($wiki, $wp));
    $w->ctx("wiki", $wiki);
    $w->ctx("page", $wp);
    $w->ctx("attachments", $w->service("File")->getAttachments($wp));
    $w->ctx("title", $wiki->title . " - " . $wp->name);
    $w->ctx("editForm", Html::multiColForm($editForm, "/wiki/edit/{$wiki->name}/{$wp->name}", "POST", "Save", null, null, 
        Html::box("/wiki/markup","Markup Help",true)
    ));
}