<?php

function resetpassword_GET(Web $w) {
	$email = $w->request('email'); // email
	$token = $w->request('token'); // token
	
	$user = $w->getObject("User", array("token", $token));
	$validData = false;
	if (!empty($user)){
		$user_contact = $user->getContact();
		if (!empty($user_contact)){
			if ($user_contact->email == $email){
				// We have passed the test
				$password_form = Html::form(array(
					array("Enter new password", "section"),
					array("New password", "password", "password"),
					array("Confirm password", "password", "password_confirm"),
				), $w->localUrl("auth/resetpassword?email=$email&token=$token"), "POST", "Reset");
				$w->out($password_form);
				$validData = true;
			}
		}
	}
	if (!$validData){
		$w->logWarn("Password reset attempt failed with email: $email, token: $token");
		$w->out("Invalid email or token, this incident has been logged");
	}
}

function resetpassword_POST(Web $w) {
	$email = $w->request('email'); // email
	$token = $w->request('token'); // token
	
	$user = $w->getObject("User", array("token", $token));
	$validData = false;
	if (!empty($user)){
		$user_contact = $user->getContact();
		if (!empty($user_contact)){
			if ($user_contact->email == $email){
				
				$validData = true;
			}
		}
	}
	if (!$validData){
		//$w->logWarn("Password reset attempt failed with email: $email, token: $token");
		$w->out("Invalid email or token, this incident has been logged");
	}
}
