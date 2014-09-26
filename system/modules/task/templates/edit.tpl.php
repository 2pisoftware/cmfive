<div class="tabs">
    <div class="tab-head">
        <a href="#details">Task Details</a>
        <?php if (!empty($task->id)) : ?>
            <a href="#timelog">Time Log</a>
            <a href="#comments">Comments</a>
            <a href="#attachments">Attachments</a>
            <?php if ($task->getCanINotify()):?><a href="#notification">Notifications</a><?php endif;?>
        <?php endif; ?>
    </div>
    <div class="tab-body">
        <div id="details" class="clearfix">
            <div class="row-fluid clearfix">
                <div class="row-fluid columns">
                    <?php 
                        // Note the extra buttons only show when the task_type object
                        $tasktypeobject = $task->getTaskTypeObject(); 
                        echo !empty($tasktypeobject) ? $tasktypeobject->displayExtraButtons($task) : null; 
                    ?>
                </div>
                <div class="row-fluid clearfix">
                    <div class="small-12 large-9">
                        <?php echo $form; ?>
                    </div>
                    <div class="small-12 large-3 right">
                        <div class="small-12 panel" id="tasktext" style="display: none;"></div>
                        <div class="small-12 panel clearfix" id="formfields" style="display: none;"></div>
                        <div class="small-12 panel clearfix" id="formdetails" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($task->id)) : ?>
            <div id="timelog">
                <?php 
                    if (!empty($task->assignee_id) && ($task->assignee_id == $w->Auth->user()->id)) :
			             echo Html::box(WEBROOT."/task/addtime/".$task->id," Add Time Log entry ",true);
                    else : ?>
                        <p>Note: you can add time logs when you're assigned to this task</p>
                    <?php endif;
                    if (!empty($timelog)) {
                        echo $timelog;
                    }
                ?>
            </div>
            <div id="comments">
                <?php echo $w->partial("listcomments",array("object"=>$task,"redirect"=>"task/edit/{$task->id}#comments"), "admin"); ?>
            </div>
            <div id="attachments">
                <?php echo $w->partial("listattachments",array("object"=>$task,"redirect"=>"task/edit/{$task->id}#attachments"), "file"); ?>
            </div>
            <?php if ($task->getCanINotify()):?>
            <div id="notification" class="clearfix">
                <div class="row small-12">
                    <h4>If you do not set notifications for this Task then the default settings for this Task group will be used</h4>
                </div>
                <?php echo $tasknotify;?>
            </div>
            <?php endif;?>
        <?php endif; ?>
    </div>
</div>
<script language="javascript">
    // Force an ajax request initially, because if the group id is provided
    // and this doesn't exist then the user would have to reselect the taskgroup
    // manually, which is bad.
    var initialChange = <?php echo (!empty($task->id) ? "false" : "true"); ?>;

    $(document).ready(function() {
        bindTypeChangeEvent();
        $("select[id='task_group_id']").trigger("change");
        $("#task_type").trigger("change");
    });
    
    $("select[id='task_group_id']").on("change", function() {
        $("#formfields").hide().html("");
        $("#tasktext").hide().html("");
        
        $.getJSON("/task/taskAjaxSelectbyTaskGroup/" + $(this).val() + "/<?php echo !empty($task->id) ? $task->id : null; ?>",
            function(result) {
                if (initialChange) {
                    $('#task_type').parent().html(result[0]);
                    $('#priority').parent().html(result[1]);
                    $('#first_assignee_id').parent().html(result[2]);
                    $('#status').html(result[4])
                }
                initialChange = true;
                $('#tasktext').html(result[3]);
                $("#tasktext").fadeIn();
                
                bindTypeChangeEvent();  
            }
        );
    });
    
    function bindTypeChangeEvent() {
        $("#task_type").on("change", function(event) {
//            $.getJSON("/task/ajaxGetTaskTypeFormFields?task_type=" + $("#task_type").val() + "&task_group_id=" + $("#task_group_id").val(),
//                function(result) {
//                    if (result.length > 0) {
//                        $("#formfields").html(result);
//                        $("#formfields").fadeIn();
//                    }
//                }
//            );
            $.getJSON("/task/ajaxGetFieldForm/" + $("#task_type").val() + "/" + $("#task_group_id").val() + "/<?php echo !empty($task->id) ? $task->id : ''; ?>",
                function(result) {
                    console.log("Extra details callback: " + result);
                    if (result[0]) {
                        $("#formfields").html(result[0]);
                        $("#formfields").fadeIn();
                    }
                }
            );
            <?php if (!empty($task->id)) : ?>
                var task_type_value = document.getElementById("task_type").value;
                if (task_type_value.length > 0) {
                    $("#formdetails").hide();
                    $.getJSON("/task/ajaxGetExtraDetails/<?php echo $task->id; ?>/" + task_type_value, function(result) {
                        
                        if (result[0]) {
                            $("#formdetails").html(result[0]);
                            $("#formdetails").fadeIn();
                        }
                    });
                }
            <?php endif; ?>
        });
    }
    
    // Submit both forms 
    $("#edit_form, #form_fields_form").submit(function() {
        toggleModalLoading();
        var edit_form = {};
        var extras_form = {};
        $.each($('#edit_form').serializeArray(), function(){
            edit_form[this.name] = this.value;
        });
        $.each($('#form_fields_form').serializeArray(), function(){
            extras_form[this.name] = this.value;
        });
        
        var action = $(this).attr('action');
        $.ajax({
            url  : action,
            type : 'POST',
            data : {
                '<?php echo \CSRF::getTokenId(); ?>': '<?php echo \CSRF::getTokenValue(); ?>', 
                'edit': edit_form, 
                'extra': extras_form
            },
            complete: function() {
               window.location.reload(); 
            }
        });
        return false;
    });
    
</script>