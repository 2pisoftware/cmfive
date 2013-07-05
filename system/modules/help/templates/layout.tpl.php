<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/css/style.css" />
        <script type="text/javascript" src="<?=$webroot?>/js/jquery-1.4.2.min.js" ></script>
		<script src="<?=WEBROOT?>/js/flowplayer/flowplayer-3.2.4.min.js"></script>
    </head>
    <body>
    <table height="600" width="100%">
    <tr><td valign="top">
    <?if ($module_toc):?>
    <a href="<?=WEBROOT.'/help/view/'.$module_toc?>"><?=$module_title?></a>&nbsp;:&nbsp;
    <?endif;?>
    <a href="<?=WEBROOT.'/help/toc'?>">Contents</a>&nbsp;:&nbsp;
    <a href="<?=WEBROOT.'/help/view/help/onhelp'?>">Help on Help</a>&nbsp;:&nbsp;
    <hr />
    </td></tr>
    <tr><td valign="top" height="100%">
    <?=$body?>
    </td></tr>
    <tr><td valign="bottom">
    <hr/>
     Copyright <?=date('Y');?> <a href="<?=$w->moduleConf('main','company_url')?>"><?=$w->moduleConf('main','company_name')?></a>
    </td></tr>
    </table>
	</body>
</html>	