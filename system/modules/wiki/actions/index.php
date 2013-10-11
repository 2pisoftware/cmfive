<?php

function index_ALL(Web $w) {
    $wiki = $page = null;
    WikiLib::wiki_navigation($w, $wiki, $page);
    $w->ctx("wikis", $w->Wiki->getWikis());
}