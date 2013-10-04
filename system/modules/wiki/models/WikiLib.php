<?php

class WikiLib {

    static function wiki_navigation(Web $w, $wiki, $page) {
        if (!empty($wiki)){
            if (property_exists($wiki, "title")) {
                $w->ctx("title", $wiki->title . (!empty($page) ? " - " . $page : ''));
            }
        }
        
        $nav = !empty($nav) ? $nav : array();
        if ($w->Auth->loggedIn()) {
            $w->menuLink("wiki/index", "Wiki List", $nav);
            $w->menuBox("wiki/createwiki", "New Wiki", $nav);
        }
        $w->ctx("navigation", $nav);
    }

    /**
     * Returns HTML transformed from wiki markup
     * @param $page
     */
    static function wiki_format_textile($wiki, $page) {
        require_once 'textile/classTextile.php';
        $textile = new Textile();
        $body = $textile->TextileThis($page->body);

        // replace wiki links
        $wn = $wiki->name;
        $body = preg_replace("/\[\[([a-zA-Z0-9]+)\]\]/", "<a href='" . WEBROOT . "/wiki/view/" . $wn . "/\\1'>\\1</a>", $body);
        return $body;
    }

    /**
     * Returns HTML transformed from wiki markup
     * @param $page
     */
    static function wiki_format_creole($wiki, $page) {
        require_once 'creole/creole.php';
        $creole = new creole(
                array(
            'link_format' => WEBROOT . "/wiki/view/" . $wiki->name . "/%s",
            'interwiki' => array(
                'Wikipedia' => 'http://en.wikipedia.org/wiki/%s'
            )
                )
        );

        $options = null;
        $body = $creole->parse($page->body);

        return $body;
    }

}