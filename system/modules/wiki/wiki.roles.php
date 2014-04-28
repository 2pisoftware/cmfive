<?php
function role_wiki_user_allowed(Web $w,$path) {
    return preg_match("/wiki(-.*)?\//",$path);
}