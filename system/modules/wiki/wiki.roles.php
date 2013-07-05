<?php
function role_wiki_user_allowed(&$w,$path) {
    return preg_match("/wiki(-.*)?\//",$path);
}