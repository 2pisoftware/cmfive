<style>
	button.cancelbutton {
		display: none;
	}
</style>

<div class="row">
	<div class="small-12 columns">
		<h3>Installation</h3>
	</div>
</div>

<div class="row">
	<div class="small-12 medium-2 columns">
		Step <?php echo $step; ?> out of 4
	</div>
	<div class="small-12 medium-10 columns">
		<div class="progress small-12 success radius">
			<span class="meter" style="width: <?php echo $step * 25; ?>%"></span>
		</div>
	</div>
</div>

<div class="row-fluid">
	<?php if( isset($error) ): ?>
		<div data-alert class="alert-box alert">
			<?php echo $error; ?>
		</div>
	<?php endif; ?>
	<?php if( isset($info) ): ?>
		<div data-alert class="alert-box success">
			<?php echo $info; ?>
		</div>
	<?php endif; ?>
</div>

<div class="row">
	<?php if ($step == 1 || $step == 2) :
		echo $form_details;
		if ($step == 2 && !empty($form_database)) :
			echo $form_database;
		endif;
		
	elseif ( $step == 3 ): ?>
	<form method="post" action="<?php echo $form_action; ?>">
		<fieldset>
			<legend>General configuration</legend>
			<input type="hidden" value="<?php echo $_POST['db_hostname']; ?>" name="db_hostname" />
			<input type="hidden" value="<?php echo $_POST['db_username']; ?>" name="db_username" />
			<input type="hidden" value="<?php echo $_POST['db_password']; ?>" name="db_password" />
			<input type="hidden" value="<?php echo $_POST['db_port']; ?>" name="db_port" />
			<input type="hidden" value="<?php echo $_POST['db_driver']; ?>" name="db_driver" />
			<input type="hidden" value="<?php echo $_POST['db_database']; ?>" name="db_database" />
			<h4>Application information</h4>
			<label>Application name</label><input type="text" placeholder="Cmfive" value="<?php echo empty($_POST['app_name']) ? 'Cmfive' : $_POST['app_name']; ?>" name="app_name" />
			<label>Company name</label><input type="text" placeholder="2pi Software" value="<?php echo empty($_POST['company_name']) ? '2pi Software' : $_POST['company_name']; ?>" name="company_name" />
			<label>Company url</label><input type="url" placeholder="http://2pisoftware.com" value="<?php echo empty($_POST['company_url']) ? 'http://2pisoftware.com' : $_POST['company_url']; ?>" name="company_url" />
			<hr />
			<h4>Timezone</h4>
			<label>Timezone</label><input type="text" placeholder="Australia/Sydney" value="<?php echo empty($_POST['timezone']) ? 'Australia/Sydney' : $_POST['timezone']; ?>" name="timezone" />
			<hr />
			<h4>Email</h4>
			<label>Layer</label><input type="text" placeholder="smtp" value="<?php echo empty($_POST['email_layer']) ? 'smtp' : $_POST['email_layer']; ?>" name="email_layer" />
			<label>Host</label><input type="text" placeholder="smtp.gmail.com" value="<?php echo empty($_POST['email_host']) ? 'smtp.gmail.com' : $_POST['email_host']; ?>" name="email_host" />
			<label>Port</label><input type="text" placeholder="465" value="<?php echo empty($_POST['email_port']) ? '465' : $_POST['email_port']; ?>" name="email_port" />
			<label>Authentication required</label>
			<div class="row">
				<div class="columns large-1">
					<div class="switch tiny round">
						<input id="email_auth_true" checked="checked" required="true" type="radio" name="email_auth" value="true" />
						<label for="email_auth_true"></label>
					</div>
				</div>
				<div class="columns large-1">
					<label for="email_auth_true">Yes</label>
				</div>
				<div class="columns large-1">
					<div class="switch tiny round">
						<input id="email_auth_false" required="true" type="radio" name="email_auth" value="false" />
						<label for="email_auth_false">No</label>
					</div>
				</div>
				<div class="columns large-1">
					<label for="email_auth_false">No</label>
				</div>
				<div class="columns large-8"></div>
			</div>
			<label>Username</label><input type="text" placeholder="username" value="<?php echo empty($_POST['email_username']) ? '' : $_POST['email_username']; ?>" name="email_username" />
			<label>Password</label><input type="password" placeholder="password" value="<?php echo empty($_POST['email_password']) ? '' : $_POST['email_password']; ?>" name="email_password" />
			<hr />
			<h4>REST</h4>
			<p>Use the API_KEY to authenticate with username and password</p>
			<label>API Key</label><input type="text" placeholder="password" value="<?php echo empty($_POST['rest_api_key']) ? 'abcdefghijklmnopqrstuvwxyz1234567890' : $_POST['rest_api_key']; ?>" name="rest_api_key" />
			<button class="button" type="submit">Complete configuration</button>
		</fieldset>
	</form>
<?php elseif($step == 4): ?>
	<h4>Configuration complete!</h4>
	<button class="button" type="button" onclick="window.location='/';">Login</button>
<?php endif; ?>
</div>