<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo!empty($title) ? ' - ' . $title : ''; ?></title>
        <link rel="icon" href="<?php echo $webroot; ?>/templates/img/favicon.png" type="image/png"/>

        <!-- Test foundation include -->
        <!-- 
            <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>/system/js/foundation-5.0.2/css/normalize.css" />
            <link rel="stylesheet" type="text/css" href="<?php echo $webroot; ?>/system/js/foundation-5.0.2/css/foundation.css" />
            <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/foundation-5.0.2/js/modernizr.js"></script>
        -->
        <?php
            $w->enqueueStyle(array("name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000));
            $w->enqueueStyle(array("name" => "tablesorter.css", "uri" => "/system/templates/css/tablesorter.css", "weight" => 990));
            $w->enqueueStyle(array("name" => "datePicker.css", "uri" => "/system/templates/css/datePicker.css", "weight" => 980));
            $w->enqueueStyle(array("name" => "jquery-ui-1.8.13.custom.css", "uri" => "/system/templates/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css", "weight" => 970));
            $w->enqueueStyle(array("name" => "liveValidation.css", "uri" => "/system/templates/css/liveValidation.css", "weight" => 960));
            $w->enqueueStyle(array("name" => "colorbox.css", "uri" => "/system/templates/js/colorbox/colorbox/colorbox.css", "weight" => 950));
            $w->enqueueStyle(array("name" => "jquery.asmselect.css", "uri" => "/system/templates/css/jquery.asmselect.css", "weight" => 940));
            
            $w->enqueueScript(array("name" => "jquery-1.4.2.min.js", "uri" => "/system/templates/js/jquery-1.4.2.min.js", "weight" => 1000));
            $w->enqueueScript(array("name" => "jquery.tablesorter.js", "uri" => "/system/templates/js/tablesorter/jquery.tablesorter.js", "weight" => 990));
            $w->enqueueScript(array("name" => "jquery.tablesorter.pager.js", "uri" => "/system/templates/js/tablesorter/jquery.tablesorter.pager.js", "weight" => 980));
            $w->enqueueScript(array("name" => "jquery.colorbox-min.js", "uri" => "/system/templates/js/colorbox/colorbox/jquery.colorbox-min.js", "weight" => 970));
            $w->enqueueScript(array("name" => "jquery-ui-1.8.13.custom.min.js", "uri" => "/system/templates/js/jquery-ui-new/js/jquery-ui-1.8.13.custom.min.js", "weight" => 960));
            $w->enqueueScript(array("name" => "jquery-ui-timepicker-addon.js", "uri" => "/system/templates/js/jquery-ui-timepicker-addon.js", "weight" => 950));
            $w->enqueueScript(array("name" => "livevalidation.js", "uri" => "system/templates/js/livevalidation.js", "weight" => 940));
            $w->enqueueScript(array("name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 995));
            $w->enqueueScript(array("name" => "jquery.asmselect.js", "uri" => "/system/templates/js/jquery.asmselect.js", "weight" => 920));
            $w->enqueueScript(array("name" => "boxover.js", "uri" => "/system/templates/js/boxover.js", "weight" => 910));
            $w->enqueueScript(array("name" => "ckeditor.js", "uri" => "/system/templates/js/ckeditor/ckeditor.js", "weight" => 900));
            $w->enqueueScript(array("name" => "Chart.js", "uri" => "/system/templates/js/chart-js/Chart.js", "weight" => 890));
            
            $w->outputStyles();
            $w->outputScripts();
        ?>
        <script type="text/javascript">

            var current_tab = 0;

            function switchTab(num) {
                if (num == current_tab)
                    return;
                $('#tab-' + current_tab).hide();
                $('#tab-link-' + current_tab).removeClass("active");
                $('#tab-' + num).show().addClass("active");
                $('#tab-link-' + num).addClass("active");
                current_tab = num;
            }

            $(document).ready(function() {
                $(".msg").delay(3000).fadeOut(3000);
                $(".error").delay(6000).fadeOut(3000);
                $("table.tablesorter").tablesorter({dateFormat: "uk", widthFixed: true, widgets: ['zebra']});
                <?php $tab = $w->request('tab');
                if (!empty($tab)) :
                    ?>
                    switchTab("<?php echo $tab; ?>");
                <?php else: ?>
                    
                    $(".tab-head").children("a").each(function() {
                        $(this).bind("click", {alink: this}, function(event) {
                            changeTab(event.data.alink.hash);
                        });
                    });

                    // Change tab if hash exists
                    var hash = window.location.hash.split("#")[1];
                    if (hash && hash.length > 0) {
                        changeTab(hash);
                    } else {
                        $(".tab-head > a:first").trigger("click");
                    }
                <?php endif; ?>
            });

            // Try and prevent multiple form submissions
            $("input[type=submit]").click(function() {
                $(this).hide();
            });
            $(document).bind('cbox_complete', function() {
                $("input[type=submit]").click(function() {
                    $(this).hide();
                });
            });

        </script>

        <?php echo!empty($htmlheader) ? $htmlheader : ''; ?>
    </head>
    <body>
        <table width="100%" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2">
                    <div id="dolphincontainer">
                        <div id="dolphinnav">
                            <?php
                            if ($w->Auth->allowed('help/view')) {
                                $top_navigation[] = Html::box(WEBROOT . "/help/view/" . $w->_module . ($w->_submodule ? "-" . $w->_submodule : "") . "/" . $w->_action, "HELP", false, true, 750, 500);
                            }
                            echo Html::ul($top_navigation);
                            ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td valign="top" BGCOLOR="#6FA9C7" width="202px">                
                    <?php if ($w->Auth->allowed('search/results')) : ?>
                        <div class="box">
                            <div class="boxtitle flt">Search</div>
                            <div class="menubg flt">
                                <form action="<?php echo $webroot; ?>/search/results" method="GET">
                                    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
                                    <input style="width: 182px; margin-top: 10px; margin-left:8px;margin-bottom: 5px;" type="text" name="q" id="q" value="<?php echo!empty($_REQUEST['q']) ? $_REQUEST['q'] : ''; ?>"/>
                                    <span style="margin-left:8px;">
                                        <?php echo Html::select("idx", $w->service('Search')->getIndexes(), (!empty($_REQUEST['idx']) ? $_REQUEST['idx'] : null), null, null, "Search All"); ?>
                                    </span>
                                    <input style="padding-left:15px;padding-right:15px;margin-right:10px;margin-bottom:10px;" type="submit" value="Search"/>
                                    <input type="hidden" name="p" value="1"/>
                                    <input type="hidden" name="ps" value="25"/>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($navigation)): ?>
                        <div class="box">
                            <div class="boxtitle flt"><?php echo ucfirst($module); ?></div>
                            <div class="menubg flt">
                                <?php echo Html::ul($navigation, null, "menu flt"); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                    if (!empty($boxes)) {
                        foreach ($boxes as $btitle => $box) {
                            ?>
                            <div class="box">
                                <div class="boxtitle flt"><?php echo ucfirst($btitle); ?></div>
                                <div class="menubg flt">
                                    <?php echo $box; ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <?php if ($w->Auth->user()): ?>
                        <div class="box">
                            <div class="boxtitle flt">Hi, <?php echo $w->Auth->user()->getShortName(); ?></div>
                            <div class="menubg flt">
                                <?php
                                $n = array(
                                    $w->menuBox("auth/profile/box", "Profile"),
                                    $w->menuLink("auth/logout", "Logout"),
                                );
                                ?>
                                <?php echo Html::ul($n, null, "menu flt"); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </td>

                <td valign="top" height="100%">
                    <div id="center">
                        <div id="body">
                            <div class="content-header"><?php echo!empty($title) ? $title : ucfirst($w->currentModule()); ?></div>
                            <?php if (!empty($error)): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
                            <?php
                            // Printing for errors from DbObject validation
                            if (!empty($_SESSION["errors"])) {
                                $errors = $_SESSION["errors"];
                                unset($_SESSION["errors"]);
                                if (is_array($errors)) {
                                    foreach ($errors as $field => $err) {
                                        foreach ($err as $i => $e)
                                            echo "<div class=\"error\">" . ucfirst($field) . " has " . $e . "</div>";
                                    }
                                }
                            }
                            ?>
                            <?php if (!empty($msg)): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
                            <?php echo!empty($body) ? $body : ''; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><div id="footer">Copyright <?php echo date('Y'); ?> <a href="<?php echo $w->moduleConf('main', 'company_url'); ?>"><?php echo $w->moduleConf('main', 'company_name'); ?></a></div></td>
            </tr>
        </table>

        <!-- Test foudnation include -->
        <!-- 
        <script type="text/javascript" src="<?php echo $webroot; ?>/system/js/foundation-5.0.2/js/foundation.min.js"></script>
        <script>
            jQuery(document).foundation();
        </script>
        -->
    </body>

</html>
