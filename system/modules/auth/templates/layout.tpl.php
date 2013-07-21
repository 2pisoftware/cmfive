<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?=ucfirst($w->currentModule())?><?=$title?' - '.$title:''?></title>
        <link rel="icon" href="<?=$webroot?>/templates/img/favicon.png" type="image/png"/>
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/liveValidation.css" />
        <script type="text/javascript" src="<?=$webroot?>/system/js/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/livevalidation.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/main.js"></script>
        <?=$htmlheader?>
    </head>
    <body>
        <table height="100%" align="center" cellpadding="0" cellspacing="0">
        <tr><td align="center" valign="middle" height="100"><h1><?=$w->moduleConf('main','application_name')?></h1></td>
        <tr><td height="100%" align="center" valign="middle">
        <? if ($error):?><div class="error"><?=$error?></div><? endif;?>
        <? if ($msg): ?><div class="msg"><?=$msg?></div><? endif;?>
        <?=$body?>
		</td></tr>
</form>
</table>
</div>
<center>
            		
</body>
</html>
