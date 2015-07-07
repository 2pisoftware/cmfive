<div class="row-fluid" id="timelog_container">
    <div id="start_timer">
        <?php if ($w->Timelog->hasTrackingObject()) : ?>
            <a onclick="startTimer();">Start Timer</a>
        <?php else : ?>
            
        <?php endif; ?>
    </div>
    <div id="stop_timer">
        <span data-tooltip aria-haspopup="true" class="has-tip tip-bottom radius" title="<?php echo !empty($active_log) ? $active_log->object_class . ": " . $active_log->getLinkedObject()->getSelectOptionTitle() : ''; ?>">
            <a id="stop_timelog" class="button"><div id="active_log_time"></div></a>
        </span>
    </div>
        
    <script>    
        var timer = null;
        var start_time = <?php echo (!empty($active_log) && $active_log->dt_start) ? $active_log->dt_start : time(); ?>;
        
        <?php if ($w->Timelog->hasActiveLog()) : ?>
            timer = countTime();
            jQuery("#stop_timer").show();
        <?php else : ?>
            jQuery("#start_timer").show();
        <?php endif; ?>
        
        function countTime() {
            calcTimelog();
            return setInterval(function () {
                calcTimelog();
            }, 1000);
        }

        // Display elapsed time
        function calcTimelog() {
            var t = (new Date().getTime() / 1000) - start_time;
            var hours = Math.floor(t / 3600),
                minutes = Math.floor(t / 60 % 60),
                seconds = Math.floor(t % 60),
                arr = [];

            arr.push(hours < 10 ? '0' + hours : hours);
            arr.push(minutes < 10 ? '0' + minutes : minutes);
            arr.push(seconds < 10 ? '0' + seconds : seconds);
            jQuery('#active_log_time').html(arr.join(':'));
        }

        // Start timer function
        function startTimer() {
            var _object = JSON.parse(<?php echo $w->Timelog->hasTrackingObject() ? json_encode($w->Timelog->getJSTrackingObject()) : ''; ?>);
            if (_object.class && _object.id) {
                jQuery.ajax("/timelog/ajaxStart/" + _object.class + "/" + _object.id, {
                    success: function(data) {
                        var object_data = JSON.parse(data);
                        
                        start_time = (new Date().getTime() / 1000);
                        timer = countTime();
                        $("#start_timer").hide();
                        $("#stop_timer").fadeIn();
                        
                        $('#stop_timer span[data-tooltip]').attr('title', '');
                        $(document).foundation('tooltip', 'reflow');
                    }
                });
            }
        }

        // UI
        jQuery("#stop_timelog").hover(
            function() {
                if (timer) {
                    clearInterval(timer);
                }

                jQuery("#active_log_time").html("Stop");
            }, 
            function() {
                timer = countTime();
            }
        );

        // Stop timer function
        jQuery("#stop_timelog").click(function() {
            jQuery.ajax("/timelog/ajaxStop", {
                success: function(data) {
                    jQuery("#stop_timer").hide();
                    jQuery("#start_timer").fadeIn();
                }
            });
        });
    </script>
</div>