<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo!empty($title) ? ' - ' . $title : ''; ?></title>
<!--        <link rel="icon" href="<?php // echo WEBROOT; ?>/templates/img/favicon.png" type="image/png"/>-->

        <?php
        $w->enqueueStyle(array("name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010));
        $w->enqueueStyle(array("name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005));
        $w->enqueueStyle(array("name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000));
        $w->enqueueStyle(array("name" => "tablesorter.css", "uri" => "/system/templates/css/tablesorter.css", "weight" => 990));
        $w->enqueueStyle(array("name" => "datePicker.css", "uri" => "/system/templates/css/datePicker.css", "weight" => 980));
        $w->enqueueStyle(array("name" => "jquery-ui-1.8.13.custom.css", "uri" => "/system/templates/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css", "weight" => 970));
        $w->enqueueStyle(array("name" => "liveValidation.css", "uri" => "/system/templates/css/liveValidation.css", "weight" => 960));
        $w->enqueueStyle(array("name" => "colorbox.css", "uri" => "/system/templates/js/colorbox/colorbox/colorbox.css", "weight" => 950));
        $w->enqueueStyle(array("name" => "jquery.asmselect.css", "uri" => "/system/templates/css/jquery.asmselect.css", "weight" => 940));
        $w->enqueueStyle(array("name" => "foundation-icons.css", "uri" => "/system/templates/font/foundation-icons/foundation-icons.css", "weight" => 930));
        $w->enqueueStyle(array("name" => "pickadate.classic.css", "uri" => "/system/templates/js/pickadate.js-3.5.2/lib/compressed/themes/classic.css", "weight" => 920));
        $w->enqueueStyle(array("name" => "pickadate.classic.date.css", "uri" => "/system/templates/js/pickadate.js-3.5.2/lib/compressed/themes/classic.date.css", "weight" => 919));
        $w->enqueueStyle(array("name" => "pickadate.classic.time.css", "uri" => "/system/templates/js/pickadate.js-3.5.2/lib/compressed/themes/classic.time.css", "weight" => 918));
        $w->enqueueStyle(array("name" => "codemirror.css", "uri" => "/system/templates/js/codemirror-4.4/lib/codemirror.css", "weight" => 900));
        
        $w->enqueueScript(array("name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1010));
        $w->enqueueScript(array("name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 1000));
        $w->enqueueScript(array("name" => "jquery.tablesorter.js", "uri" => "/system/templates/js/tablesorter/jquery.tablesorter.js", "weight" => 990));
        $w->enqueueScript(array("name" => "jquery.tablesorter.pager.js", "uri" => "/system/templates/js/tablesorter/addons/pager/jquery.tablesorter.pager.js", "weight" => 980));
        $w->enqueueScript(array("name" => "jquery.colorbox-min.js", "uri" => "/system/templates/js/colorbox/colorbox/jquery.colorbox-min.js", "weight" => 970));
        $w->enqueueScript(array("name" => "jquery-ui-1.10.4.custom.min.js", "uri" => "/system/templates/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js", "weight" => 960));
//        $w->enqueueScript(array("name" => "jquery-ui-1.8.13.custom.min.js", "uri" => "/system/templates/js/jquery-ui-new/js/jquery-ui-1.8.13.custom.min.js", "weight" => 960));
        $w->enqueueScript(array("name" => "pickadate.js", "uri" => "/system/templates/js/pickadate.js-3.5.2/lib/compressed/picker.js", "weight" => 955));
        $w->enqueueScript(array("name" => "pickadate.date.js", "uri" => "/system/templates/js/pickadate.js-3.5.2/lib/compressed/picker.date.js", "weight" => 954));
        $w->enqueueScript(array("name" => "pickadate.time.js", "uri" => "/system/templates/js/pickadate.js-3.5.2/lib/compressed/picker.time.js", "weight" => 953));
        $w->enqueueScript(array("name" => "jquery-ui-timepicker-addon.js", "uri" => "/system/templates/js/jquery-ui-timepicker-addon.js", "weight" => 950));
        $w->enqueueScript(array("name" => "livevalidation.js", "uri" => "/system/templates/js/livevalidation.js", "weight" => 940));
        $w->enqueueScript(array("name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 995));
        $w->enqueueScript(array("name" => "jquery.asmselect.js", "uri" => "/system/templates/js/jquery.asmselect.js", "weight" => 920));
        $w->enqueueScript(array("name" => "boxover.js", "uri" => "/system/templates/js/boxover.js", "weight" => 910));
        $w->enqueueScript(array("name" => "ckeditor.js", "uri" => "/system/templates/js/ckeditor/ckeditor.js", "weight" => 900));
        $w->enqueueScript(array("name" => "Chart.js", "uri" => "/system/templates/js/chart-js/Chart.min.js", "weight" => 890));
        
        // Code mirror
        $w->enqueueScript(array("name" => "codemirror.js", "uri" => "/system/templates/js/codemirror-4.4/codemirror-compressed.js", "weight" => 880));
        
        $w->outputStyles();
        $w->outputScripts();
        ?>
        <script type="text/javascript">
            var $ = jQuery;
            $(document).ready(function() {
                $("table.tablesorter").tablesorter({dateFormat: "uk", widthFixed: true, widgets: ['zebra']});
                $(".tab-head").children("a").each(function() {
                    $(this).bind("click", {alink: this}, function(event) {
                        changeTab(event.data.alink.hash);
                        return false;
                    });
                });

                // Change tab if hash exists
                var hash = window.location.hash.split("#")[1];
                if (hash && hash.length > 0) {
                    changeTab(hash);
                } else {
                    $(".tab-head > a:first").trigger("click");
                }
                
                // Set up CodeMirror instances if any
                bindCodeMirror();
                
                // Adjust the breadcrumbs div if it's content is longer than the viewport
                var breadcrumbs = $('.cmfive_breadcrumbs');
                if (breadcrumbs.length) {
                    if (breadcrumbs[0].scrollWidth > $(window).width()) {
                        breadcrumbs.css('height', (breadcrumbs.height() + 20) + "px");
                    }
                }

				// Admin clear cache button function
				$('#admin_clear_cache').bind('click', function(e) {
					$('#admin_clear_cache').css('color', '#CD0000');
					$.get($(this).attr('href'), function() {
						setTimeout(function() {
							$('#admin_clear_cache').css('color', '#4B6995');
							$('#admin_clear_cache .clear_cache_icon').removeClass('fi-refresh').addClass('fi-check');
							setTimeout(function() {
								$('#admin_clear_cache').css('color', '#FFF');
								$('#admin_clear_cache .clear_cache_icon').removeClass('fi-check').addClass('fi-refresh');
							}, 500);
						}, 500);
					});
					e.preventDefault();
					return false;
				});
                // Search function shortcut listener
                $(document).on('keydown', function ( e ) {
                    if ((e.ctrlKey || e.metaKey) && e.which === 70) {
                        $('#cmfive_search_button').click();
                        return false;
                    }
                });
				globalFileUpload.init();
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
    </head>
    <body>
        <div class="loading_overlay" <?php echo $w->request('show_overlay') == null ? 'style="display:none;"' : ''; ?>>
            <div class="circle"></div>
            <div class="circle_inner"></div>
            <div class="circle_center"></div>
            <h4 class="subheader">Please wait...</h4>
        </div>
		<div class="global_file_drop_overlay" id="global_file_drop_overlay" style="display:none;">
			<div class="global_file_drop_overlay_loading" style="display:none;">
				<div class="circle"></div>
				<div class="circle_inner"></div>
				<div class="circle_center"></div>
				<h4 class="subheader">Uploading (0%)</h4>
			</div>
			<div class="global_file_drop_overlay_init">
				<h4 class="subheader">Drop files here...</h4>
			</div>
		</div>
		
		<?php if (Config::get('system.test_mode') === true) : ?>
			<div class="row-fluid">
				<div class="small-12">
					<div data-alert class="alert-box warning" style="margin-bottom: 0px; padding: 5px 0px;">
						<h4 style="font-weight: lighter; text-align: center; color: white; padding: 5px 0px 0px 0px;"><?php echo Config::get('system.test_mode_message')?></h4>
					</div>
				</div>
			</div>
		<?php endif; ?>
        
		<div class="row-fluid">
            <nav class="top-bar" data-topbar><!-- To make it that you need to click to activate dropdown use  data-options="is_hover: false" -->
                <ul class="title-area">
                    <li class="name">
                        <!--<h1><a href="/"><?php // echo str_replace("http://", "", $w->moduleConf('main', 'company_url'));   ?></a></h1>-->
                    </li>
                    <li class="toggle-topbar"><a href="">Menu</a></li>
                </ul>

                <section class="top-bar-section">
                    <!-- Right Nav Section -->
                    <ul class="right">
                        <!-- Module template injection -->
                        <?php 
                            $inject = $w->callHook('core_template', 'menu');
                            if (!empty($inject)) :
                                foreach($inject as $i) : ?>
                                    <li><?php echo $i; ?></li>
                                <?php endforeach;
                            endif;
                        ?>
                        
                        <!-- Search bar -->
                        <li><?php echo Html::box("/search", "<span class='fi-magnifying-glass show-for-medium-up'></span><span class='show-for-small'>Search</span>", false, false, null, null, null, "cmfive_search_button"); ?></li>
                        
                        <?php if ($w->Auth->user()): ?>
						<!-- Clear cache button -->
							<?php if ($w->Auth->user()->is_admin): ?>
							<li>
								<a id="admin_clear_cache" href="/admin/ajaxClearCache" onclick="return false;" title="Clear configuration cache">
									<span class="clear_cache_icon fi-refresh show-for-medium-up"></span>
									<span class="show-for-small">Clear cache</span>
								</a>
							</li>
							<?php endif; ?>
                        <!-- User Profile drop down -->
                            <li class="has-dropdown">
                                <a href="#">
									<span class="fi-torso show-for-medium-up"></span>
									<span class="show-for-small">Account</span>
								</a>
                                <?php
                                echo Html::ul(
                                    array(
                                        $w->menuBox("auth/profile/box", $w->Auth->user()->getShortName()),
                                        $w->menuLink("auth/logout", "Logout")
                                    ), null, "dropdown");
                                ?>    
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- Left Nav Section -->
                    <ul class="left">
                        <?php if ($w->Auth->loggedIn()) : ?>
                            <li><?php echo $w->menuLink($w->Main->getUserRedirectURL(), "<span class='fi-home show-for-medium-up'></span><span class='show-for-small'>Home</span>"); ?></li>
                            <li class="divider"></li>
                            <?php foreach ($w->modules() as $module) {
                                // Check if config is set to display on topmenu
                                if (Config::get("{$module}.topmenu") && Config::get("{$module}.active")) :
                                    // Check for navigation
                                    $service_module = ucfirst($module);
                                    $menu_link = method_exists($w->$service_module, "menuLink") ? $w->$service_module->menuLink() : $w->menuLink($module, ucfirst($module));
                                    if ($menu_link !== false) :
                                        if (method_exists($module . "Service", "navigation")) : ?>
                                            <li class="has-dropdown <?php echo $w->_module == $module ? 'active' : ''; ?>" id="topnav_<?php echo $module; ?>">
                                            <?php // Try and get a badge count for the menu item
                                                echo $menu_link;
                                                echo Html::ul($w->service($module)->navigation($w), null, "dropdown"); ?>
                                            </li>
                                        <?php else: ?>
                                            <li <?php echo $w->_module == $module ? 'class="active"' : ''; ?>><?php echo $menu_link; ?></li>
                                        <?php endif; ?>
                                        <li class="divider"></li>
                                    <?php endif;
                                endif;
                            }
                        
                            if ($w->Auth->allowed('help/view')) : ?>
                                <li><?php echo Html::box(WEBROOT . "/help/view/" . $w->_module . ($w->_submodule ? "-" . $w->_submodule : "") . "/" . $w->_action, "<span class='fi-q show-for-medium-up'>?</span><span class='show-for-small'>Help</span>", false, true, 750, 500, "isbox", null, null, null, 'cmfive-help-modal'); ?> </li>
                            <?php endif;
                        endif; ?>
                    </ul> <!-- End left nav section -->
                </section>
            </nav>
        </div>

        <!-- Breadcrumbs -->
        <div class="row-fluid">
            <?php echo Html::breadcrumbs(array(), $w); ?>
        </div>
        
        <div class="row-fluid body">
            <?php // Body section w/ message and body from template ?>
            <div class="row-fluid <?php // if(!empty($boxes)) echo "medium-10 small-12 "; ?>">
                <?php if (empty($hideTitle) && !empty ($title)):?>
                <div class="row-fluid small-12">
                    <h3 class="header"><?php echo $title; ?></h3>
                </div>
                <?php endif;?>
                <?php if (!empty($error) || !empty($msg)) : ?>
                    <?php $type = !empty($error) ? array("name" => "error", "class" => "warning") : array("name" => "msg", "class" => "info"); ?>
                    <div data-alert class="alert-box <?php echo $type["class"]; ?>">
                        <?php echo $$type["name"]; ?>
                        <a href="#" class="close">&times;</a>
                    </div>
                <?php endif; ?>

                <div class="row-fluid" style="overflow: hidden;">
                    <?php echo !empty($body) ? $body : ''; ?>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div id="footer">
                Copyright &#169; <?php echo date('Y'); ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $w->moduleConf('main', 'company_url'); ?>"><?php echo $w->moduleConf('main', 'company_name'); ?></a>
            </div>
        </div>

        <div id="cmfive-modal" class="reveal-modal xlarge" data-reveal></div>
        <div id="cmfive-help-modal" class="reveal-modal xlarge" data-reveal></div>
        
        <script type="text/javascript" src="/system/templates/js/foundation-5.5.0/js/foundation.min.js"></script>
        <script type="text/javascript" src="/system/templates/js/foundation-5.5.0/js/foundation/foundation.clearing.js"></script>
        <script>
            $(document).foundation({
                reveal : {
                    animation_speed: 150,
                    animation: 'fade'
                },
				accordion: {
					multi_expand: true,
				}
			});
            
            var modal_history = [];
            var modal_history_pop = false;
            
            // Automatically append the close 'x' to reveal modals
            $(document).on('opened', '[data-reveal]', function () {
                $(this).append("<a class=\"close-reveal-modal\">&#215;</a>");
                modal_history.push();
                bindModalLinks();
            });
            
            function bindModalLinks() {
                // Stop a links and follow them inside the reveal modal
                $("#cmfive-modal a:not(#modal-back)").click(function(event) {                    
                    if ($(this).hasClass("close-reveal-modal")) {
                        $("#cmfive-modal").foundation("reveal", "close");
                    } else {
						// No one is using the help system at the moment
						// Therefore no real need for a dynamic modal history
						return true;
                    }
                    return false;
                });
                
				$("#cmfive-help-modal a:not(#modal-back)").click(function(event) {                    
                    if ($(this).hasClass("close-reveal-modal")) {
                        $("#cmfive-modal").foundation("reveal", "close");
                    } else {
                        if ($(this).attr('href')[0] === "#") {
                            return true;
                        } else {
                            // Add href to history if the href wasnt the last item in the stack and that we arent the back link
                            if (modal_history.indexOf($(this).attr('href')) !== modal_history.length) {
                                modal_history.push($(this).attr('href'));
                                modal_history_pop = true;
                            }
                            changeModalWindow($(this).closest('.reveal-modal'), $(this).attr('href'));
                        }
                    }
                    return false;
                });
                
                // Bind back traversal to modal window
                $("#cmfive-modal #modal-back, #cmfive-help-modal #modal-back").click(function(event) {
                    // event.preventDefault();
                    if (modal_history.length > 0) {
                        // When you click a link, THAT link goes onto the stack.
                        // However we want the one before it.
                        // The modal_history_pop prevents us from popping twice (if back is pressed twice in a row
                        // for example)
                        if (modal_history_pop) {
                            modal_history.pop();
                            modal_history_pop = false;
                        }
                        if (modal_history.length > 0) {
                            changeModalWindow($(this).closest('.reveal-modal'), modal_history.pop());
                        }
//                        console.log(modal_history);
                    } 
                    return false;
                });
            }
            
            // Updates the modal window by content from ajax request to uri
            function changeModalWindow(object, uri) {
                $.get(uri, function(data) {
                    object.html(data + "<a class=\"close-reveal-modal\">&#215;</a>");
                    bindModalLinks();
                });
            }
        </script>
    </body>
</html>
