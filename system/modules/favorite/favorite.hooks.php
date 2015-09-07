<?php


function favorite_core_template_menu(Web $w) {
	$response = '<link rel="stylesheet" href="/system/modules/favorite/assets/css/favorite.css" />';
	$response .= '<script src="/system/modules/favorite/assets/js/favoriteButton.js"></script>';
    $response .= '<li>' . Html::box("/favorite", "Favourites") . '</li>';
	
	return $response;
}