<?php
// add this to the search index
public $_searchable;

// format display
public function printSearchTitle() {
}
public function printSearchListing() {
}
public function printSearchUrl() {
}

// does this show in the search results?
public function canList(User $user = null) {
}

// does this display the details link?
public function canView(User $user = null) {
}
