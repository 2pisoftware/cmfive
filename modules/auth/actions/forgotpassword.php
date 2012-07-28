<?php
function forgotpassword_GET(Web $w) {
	$loginform = Html::form(array(
	array("Reset Password","section"),
	array("Your Login","text","login"),
	),$w->localUrl("auth/forgotpassword"),"POST","Reset");
	$w->out($loginform);	
}

function forgotpassword_POST(Web $w) {
	$login = $w->request("login");
	$user = $w->Auth->getUserForLogin($login);
	if (!$user) {
		$w->error("Wrong login.","/auth/login");
	}
	// do something
	
	// explain
	$w->msg("Please check you email for instructions how to reset your password.","/auth/login");
}