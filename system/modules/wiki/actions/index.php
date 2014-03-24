<?php

function index_ALL(Web $w) {
    $wiki = $page = null;
    $w->Wiki->navigation($w, $wiki, $page);
    $w->ctx("wikis", $w->Wiki->getWikis());
}