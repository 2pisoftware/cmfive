<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?=ucfirst($w->currentModule())?><?=$title?' - '.$title:''?></title>
        <link rel="icon" href="<?=$webroot?>/templates/img/favicon.png" type="image/png"/>
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/tablesorter.css" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/datePicker.css" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/system/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/liveValidation.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="<?=$webroot?>/templates/css/colorbox.css" />
        <link rel="stylesheet" type="text/css" href="<?=$webroot?>/templates/css/jquery.asmselect.css" />

        <script type="text/javascript" src="<?=$webroot?>/system/js/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/tablesorter/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/colorbox/colorbox/jquery.colorbox-min.js"></script>        
        <script type="text/javascript" src="<?=$webroot?>/system/js/jquery-ui-new/js/jquery-ui-1.8.13.custom.min.js"></script>
		<script type="text/javascript" src="<?=$webroot?>/system/js/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/livevalidation.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/main.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/jquery.asmselect.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="<?=$webroot?>/system/js/boxover.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $(".msg").fadeOut(3000);
                $("table.tablesorter").tablesorter({dateFormat: "uk", widthFixed: true, widgets: ['zebra']});
            });
        </script>

        <?=$htmlheader?>
    </head>
    <body>
        <table width="100%" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2">
                    <div id="dolphincontainer">
                    	<div id="dolphinnav">
                    	<?
                    	if ($w->Auth->allowed('help/view')) {
                    		$top_navigation[]=Html::box(WEBROOT."/help/view/".$w->_module.($w->_submodule ? "-".$w->_submodule : "")."/".$w->_action,"HELP",false,true,750,500);
                    	}
						echo Html::ul($top_navigation);
						?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td valign="top" BGCOLOR="#6FA9C7" width="202px">                
             	     <?if ($w->Auth->allowed('search/results')):?>
                	<div class="box">
                        <div class="boxtitle flt">Search</div>
                        <div class="menubg flt">
                            <form action="<?=$webroot?>/search/results" method="get">
                                <input style="width: 182px; margin-top: 10px; margin-left:8px;margin-bottom: 5px;" type="text" name="q" id="q" value="<?=$_REQUEST['q']?>"/>
                                    <span style="margin-left:8px;"><?=Html::select("idx",$w->service('Search')->getIndexes(),$_REQUEST['idx'],null,null,"Search All")?></span>
                                <input style="padding-left:15px;padding-right:15px;margin-right:10px;margin-bottom:10px;" type="submit" value="Search"/>
                                <input type="hidden" name="p" value="1"/>
                                <input type="hidden" name="ps" value="25"/>
                             </form>
                        </div>
                	</div>
                    <?endif;?>
                    <?if ($navigation):?>
                    <div class="box">
                        <div class="boxtitle flt"><?=ucfirst($module)?></div>
                        <div class="menubg flt">
                                <?=Html::ul($navigation,null,"menu flt")?>
                        </div>
                    </div>
                    <?endif;?>
                    <?
                    if ($boxes) {
                        foreach ($boxes as $btitle => $box) {
                            ?>
                    <div class="box">
                        <div class="boxtitle flt"><?=ucfirst($btitle)?></div>
                        <div class="menubg flt">
                            <?=$box?>
                        </div>
                    </div>
                            <?
                        }
                    }
                    ?>
                    <?if ($w->Auth->user()):?>
                    <div class="box">
                        <div class="boxtitle flt">Hi, <?=$w->Auth->user()->getShortName()?>!</div>
                        <div class="menubg flt">
                                <?$n=array(
                                        $w->menuBox("auth/profile/box","Profile"),
                                        $w->menuLink("auth/logout","Logout"),
                                );?>
                                <?=Html::ul($n,null,"menu flt")?>
                        </div>
                    </div>
                    <?endif;?>
                </td>

                <td valign="top" height="100%">
                    <div id="center">
                        <div id="body">
                        	<div class="content-header"><?=$title?$title:ucfirst($w->currentModule())?></div>
	                        <? if ($error):?><div class="error"><?=$error?></div><? endif;?>
                       		<? if ($msg): ?><div class="msg"><?=$msg?></div><? endif;?>
                            <?=$body?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><div id="footer">Copyright <?=date('Y');?> <a href="<?=$w->moduleConf('main','company_url')?>"><?=$w->moduleConf('main','company_name')?></a></div></td>
            </tr>
        </table>
    </body>
</html>
