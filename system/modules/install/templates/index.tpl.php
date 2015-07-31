<div class="row-fluid">
	<h3>Step <?php echo $step; ?></h3>
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
<?php if( $step == 1 ): ?>
	<form method="post" action="<?php echo $form_action; ?>">
		<fieldset>
			<legend>Database connection</legend>
			<label>Hostname</label>
			<input type="text" placeholder="Hostname" required="true" value="<?php echo empty($_POST['db_hostname']) ? 'localhost' : $_POST['db_hostname']; ?>" name="db_hostname" />
			<label>Username</label>
			<input type="text" placeholder="Username" required="true" value="<?php echo $_POST['db_username']; ?>" name="db_username" />
			<label>Password</label>
			<input type="password" placeholder="Password" required="true" value="<?php echo $_POST['db_password']; ?>" name="db_password" />
			<label>Port</label>
			<input type="number" placeholder="Port" value="<?php echo $_POST['db_port']; ?>" name="db_port" />
			<label>Driver</label>
			<select name="db_driver" required="true">
				<option>mysql</option>
			</select>
			<button class="button" type="submit">Check connection</button>
		</fieldset>
	</form>
<?php elseif( $step == 2 ): ?>
	<form method="post" action="<?php echo $form_action; ?>">
		<fieldset>
			<legend>Select database</legend>
			<input type="hidden" value="<?php echo $_POST['db_hostname']; ?>" name="db_hostname" />
			<input type="hidden" value="<?php echo $_POST['db_username']; ?>" name="db_username" />
			<input type="hidden" value="<?php echo $_POST['db_password']; ?>" name="db_password" />
			<input type="hidden" value="<?php echo $_POST['db_port']; ?>" name="db_port" />
			<input type="hidden" value="<?php echo $_POST['db_driver']; ?>" name="db_driver" />
			<table>
				<tbody>
					<?php foreach($databases as $database=>$tables): ?>
					<tr>
						<td>
							<div class="switch tiny round">
								<input id="database_<?php echo $database; ?>" required="true" type="radio" name="db_database" value="<?php echo $database; ?>" />
								<label for="database_<?php echo $database; ?>"></label>
							</div>
						</td>
						<td>
							<label for="database_<?php echo $database; ?>"><?php echo $database; ?>
							<?php if(!empty($tables)): ?>
							<span class="label round alert">This database is not empty, existing tables will be removed!</span>
							<?php else: ?>
							<span class="label round success">This database is empty</span>
							<?php endif; ?>
							</label>
						</td>
					<?php endforeach; ?>
				</tbody>
			</table>
			<button class="button" type="submit">Import required tables</button>
		</fieldset>
	</form>
<?php elseif( $step == 3 ): ?>
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