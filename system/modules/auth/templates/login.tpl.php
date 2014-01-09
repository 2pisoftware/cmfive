<form method="POST" action="/auth/login">
	<?php
		$token = md5(uniqid(rand(), TRUE));
        $_SESSION['token'] = $token;
    ?>
    <input type='hidden' name='csrf_token' value='<?php echo $token; ?>' />
    
	<label for="login">Login</label>
	<input id="login" name="login" type="text" placeholder="Your login" />
	<label for="password">Password</label>
	<input id="password" name="password" type="password" placeholder="Your password" />
	<button type="submit" class="button large-5 small-12">Login</button>
	<a href="/auth/forgotpassword" class="button alert large-5 small-12 right">Forgot Password</a>
</form>