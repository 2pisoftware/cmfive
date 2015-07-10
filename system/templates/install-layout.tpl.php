<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Install Cmfive</title>

        <?php
		$w->enqueueStyle(array("name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010));
		$w->enqueueStyle(array("name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005));
		$w->outputStyles();
		$w->outputScripts();
		?>
	</head>
	<body>
		<div class="row body">
            <div class="row-fluid">
                <div class="row-fluid small-12">
                    <h3 class="header">Welcome to Cmfive</h3>
                </div>
                <div class="row-fluid" style="overflow: hidden;">
                    <?php echo !empty($body) ? $body : ''; ?>
                </div>
            </div>
        </div>
	</body>
</html>