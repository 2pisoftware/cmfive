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
	$responseString = "If this account exists then a password reset email has been just to the associated email address.";
	
	// For someone trying to gain access to a system, this is one of the
	// easiest ways to find a valid login, using the security through obscurity
	// principle, we dont tell them if it was a valid user or not, and we can log if they get it wrong
	if (!$user) {
		$w->msg($responseString,"/auth/login");
	}
	
	// explain
	$w->msg($responseString,"/auth/login");
}