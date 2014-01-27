<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' - ' . $title : ''; ?></title>
        
        <link rel="icon" href="<?php echo $webroot; ?>/templates/img/favicon.png" type="image/png"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>/templates/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>/templates/css/liveValidation.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>/system/js/foundation-5.0.2/css/normalize.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>/system/js/foundation-5.0.2/css/foundation.css" />
        <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/foundation-5.0.2/js/modernizr.js"></script>
        <?php echo !empty($htmlheader) ? $htmlheader : '';?>

    </head>
    <body>
        <!-- NEW -->
        <div class="row">
            <div class="large-6 small-10 columns small-centered">
                <div class="row small-6 small-centered">
                    <h1 style="text-align: center;"><?php echo $w->moduleConf('main','application_name'); ?></h1>
                </div>

                <?php if (!empty($error) || !empty($msg)) : ?>
                    <?php $type = !empty($error) ? array("name" => "error", "class" => "warning") : array("name" => "msg", "class" => "info"); ?>
                    <div data-alert class="alert-box <?php echo $type["class"]; ?>">
                        <?php echo $$type["name"]; ?>
                        <a href="#" class="close">&times;</a>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php echo !empty($body) ? $body : ''; ?>
                </div>
            </div>
        </div>

        <!-- OLD -->
        <!-- <table height="100%" align="center" cellpadding="0" cellspacing="0">
        <tr><td align="center" valign="middle" height="100"><h1><?php echo $w->moduleConf('main','application_name'); ?></h1></td>
        <tr><td height="100%" align="center" valign="middle">
        <?php if (!empty($error)): ?><div class="error"><?php echo $error; ?></div><?php endif;?>
        <?php if (!empty($msg)): ?><div class="msg"><?php echo $msg; ?></div><?php endif;?>
        <?php echo !empty($body) ? $body : ''; ?>
		</td></tr>
		</table> -->

        <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/foundation-5.0.2/js/jquery.js" ></script>
        <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/foundation-5.0.2/js/foundation.min.js"></script>
        <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/livevalidation.js"></script>
        <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/main.js"></script>
        <script>
            jQuery(document).foundation();
        </script>
	</body>
</html>
