<?php
/**
 * 
 *
 * @author Steve Ryan, steve@2pisystems.com, 2015
 **/
 
 
function ajaxGetFavoriteButton_ALL(Web $w) {
	$id = $w->request("id");
    $class=$w->request("class");
    echo $w->Favorite->getFavoriteButton($id,$class);
}
