<?php

function index_ALL(Web &$w) {
	$w->out(print_r($w->Search->getIndexes(),true));
}
