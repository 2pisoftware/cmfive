<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Install Cmfive <?php echo CMFIVE_VERSION; ?></title>

        <?php
			$w->enqueueStyle(array("name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010));
			$w->enqueueStyle(array("name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005));
			$w->enqueueStyle(array("name" => "install.css", "uri" => "/system/modules/install/assets/css/install.css", "weight" => 1000));
			$w->enqueueStyle(array("name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 2000));
			$w->enqueueStyle(array("name" => "jquery-ui-1.8.13.custom.css", "uri" => "/system/templates/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css", "weight" => 970));
			$w->enqueueScript(array("name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1010));
			$w->enqueueScript(array("name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 1000));
			$w->enqueueScript(array("name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 995));
			$w->enqueueScript(array("name" => "jquery-ui-1.10.4.custom.min.js", "uri" => "/system/templates/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js", "weight" => 960));
			$w->enqueueScript(array("name" => "foundation.min.js", "uri" => "/system/templates/js/foundation-5.5.0/js/foundation.min.js", "weight" => 940));
			$w->enqueueScript(array("name" => "zxcvbn-async.js", "uri" => "/system/modules/install/assets/js/zxcvbn-async.js", "weight" => 940));
			$w->outputStyles();
			$w->outputScripts();
		?>
	</head>
	<body>
		<div class="loading_overlay" <?php echo $w->request('show_overlay') == null ? 'style="display:none;"' : ''; ?>>
            <div class="circle"></div>
            <div class="circle_inner"></div>
            <div class="circle_center"></div>
            <h4 class="subheader">Please wait...</h4>
        </div>
		<div class="row body">
			<div class="row-fluid">
				<div class="columns large-12 clearfix">
					<h3 class="header"><img class="hide-for-small-down" src="/system/templates/img/cmfive-logo.png" alt="Cmfive" /> Installing Cmfive <?php echo CMFIVE_VERSION; ?></h3>
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
					<div class="small-12 medium-2 columns">
						Step <?php echo $step; ?> out of 4
					</div>
					<div class="small-12 medium-10 columns">
						<div class="progress small-12 success radius">
							<span class="meter" style="width: <?php echo $step * 25; ?>%"></span>
						</div>
					</div>
				</div>
				<?php echo !empty($body) ? $body : ''; ?>
			</div>
        </div>
	</body>
</html>