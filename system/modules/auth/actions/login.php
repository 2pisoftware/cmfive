<?php
function login_GET(Web $w) {
	$loginform = Html::form(array(
		array("Application Login","section"),
		array("Username","text","login"),
		array("Password","password","password"),
	),$w->localUrl("auth/login"),"POST","Login");
	$w->ctx("loginform",$loginform);
}

function login_POST(Web &$w) {
	if ($_POST['login'] && $_POST['password']) {
		$client_timezone = "Australia/Sydney";//$_POST['user_timezone'];
		$user = $w->Auth->login($_POST['login'],$_POST['password'],$client_timezone);
		if ($user) {
			if ($w->session('orig_path') != "auth/login") {
				$url = $w->session('orig_path');
				$w->logDebug("original path: ".$url);

				// If no url specified, go to the users defined url
				if (empty($url)){
					$url = $user->redirect_url;
				}
				$w->sessionUnset('orig_path');
				$w->redirect($w->localUrl($url));
			} else {
				$w->redirect($w->localUrl());
			}
		}
		else {
			$w->error("Login or Password incorrect","/auth/login");
		}
	} else {
		$w->error("Please enter your login and password","/auth/login");
	}
}
