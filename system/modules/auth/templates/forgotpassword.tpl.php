<form method="POST" action="/auth/forgotpassword">
	<?php
		$token = md5(uniqid(rand(), TRUE));
        $_SESSION["token"] = $token;
    ?>
    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>" />
	<label for="login">Login</label>
	<input id="login" name="login" type="text" placeholder="Your login" />
	<button type="submit" class="button large-5 small-12">Submit</button>
	<a href="/auth/login" class="button secondary large-5 small-12 right">Back</a>
</form>