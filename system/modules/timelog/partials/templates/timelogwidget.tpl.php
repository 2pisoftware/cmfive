<div class="row-fluid" id="timelog_container">
    <div id="start_timer">
        <?php if ($w->Timelog->hasTrackingObject()) : ?>
            <a onclick="openDescription();">Start Timer</a>
        <?php endif; ?>
    </div>
	
    <div id="stop_timer">
        <span data-tooltip aria-haspopup="true" class="has-tip tip-bottom radius" title="<?php echo !empty($active_log) ? $active_log->object_class . ": " . $active_log->getLinkedObject()->getSelectOptionTitle() : ''; ?>">
            <a id="stop_timelog" class="button"><div id="active_log_time"></div></a>
        </span>
    </div>
	
    <div id="timerModal" class="reveal-modal" data-reveal aria-hidden="true" role="dialog">
        <form onsubmit="saveTimer()">
                <div class="row-fluid clearfix panel">
                        <?php echo $active_object_description; ?>
                </div>
                <div class="row">
			<div class="large-12 columns">
				<h2>Start timer</h2>
			</div>
		</div>
                
		<div class="row">
			<div class="large-12 columns">
				<label>Enter Description
					<?php echo (new \Html\Form\Textarea())->setId("timelog_description")->setName("timelog_description")->setRows(8); ?>
				</label>
			</div>
		</div>
		<br/>
                <div class="row">
			<div class="large-12 columns">
				<label>Enter Start Time (Optional - Defaults to 'now')
					<?php echo(new \Html\Form\InputField([
                                                "id|name"		=> "start_time",
                                                "value"			=> !empty($active_log) ? $active_log->getTimeStart() : null,
                                                "pattern"		=> "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                                                "placeholder"	=> "12hr format: 11:30pm or 24hr format: 23:30"
                                        ])); ?>
				</label>
			</div>
		</div>
                <br/>
		<div class="row">
			<div class="large-12 columns">
				<input class="button" type="submit" value="Save">
				<button class="button secondary right" onclick="$('#timerModal').foundation('reveal', 'close');">Close</button>
			</div>
		</div>
        </form>
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

		function openDescription() {
			$('#timerModal').foundation('reveal', 'open');
                        $("#start_time").val("");
		}

        // Start timer function
        function saveTimer() {
            if ($("#start_time").val() != "") {
                console.log('pass');
                console.log($("#start_time").val());
                var startDate = parseTime($("#start_time").val());
                console.log(startDate);
                console.log((new Date()));
                if ((new Date()) <= startDate) {                    
                        alert('You cannot set the start time in future');
                    return;
                }
            }
            var _object = JSON.parse(<?php echo $w->Timelog->hasTrackingObject() ? json_encode($w->Timelog->getJSTrackingObject()) : ''; ?>);
            if (_object.class && _object.id) {
                jQuery.ajax("/timelog/ajaxStart/" + _object.class + "/" + _object.id, {
					method: "POST",
					data: {
						'description': $("#timelog_description").val(),
						'start_time': $("#start_time").val()
					},
                    success: function(data) {
                        var object_data = JSON.parse(data);
                        
                        start_time = object_data.start_time ? object_data.start_time : (new Date().getTime() / 1000);
                        timer = countTime();
                        $("#start_timer").hide();
                        $("#stop_timer").fadeIn();
                        $("#start_time").val("");
                        var selector = $('#stop_timer span[data-tooltip]').attr('data-selector');
                        $('#' + selector).html(object_data.object + ': ' + object_data.title);
                        
                        $(document).foundation('tooltip', 'reflow');
						
						$("#timelog_description").val("");
						$('#timerModal').foundation('reveal', 'close');
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
                    
                    $(document).foundation('tooltip', 'reflow');
                }
            });
        });
    </script>
</div>
