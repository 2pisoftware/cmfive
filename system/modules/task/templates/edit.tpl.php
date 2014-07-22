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
                <div class="small-12 large-9">
                    <?php echo $form; ?>
                </div>
                <div class="small-12 large-3 right">
                    <div class="small-12 panel" id="tasktext" style="display: none;">

                    </div>
                    <div class="small-12 panel" id="form_fields" style="display: none;">

                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($task->id)) : ?>
            <div id="timelog">
                <?php 
                    if (!empty($task->assignee_id) && ($task->assignee_id == $w->Auth->user()->id)) {
			echo Html::box(WEBROOT."/task/addtime/".$task->id," Add Time Log entry ",true);
                    }
                    if (!empty($timelog)) {
                        echo $timelog;
                    }
                ?>
            </div>
            <div id="comments">
                <?php echo $w->partial("listcomments",array("object"=>$task,"redirect"=>"task/viewtask/{$task->id}#comments"), "admin"); ?>
            </div>
            <div id="attachments">
                <?php echo $w->partial("listattachments",array("object"=>$task,"redirect"=>"task/viewtask/{$task->id}#attachments"), "file"); ?>
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
<script language="javascript">
    // Force an ajax request initially, because if the group id is provided
    // and this doesn't exist then the user would have to reselect the taskgroup
    // manually, which is bad.
    var initialChange = <?php echo (!empty($task->id) ? "false" : "true"); ?>;

    $(document).ready(function() {
        $("select[id='task_group_id']").trigger("change");
        $("select[id='task_type']").trigger("change");
    });

    $("select[id='task_group_id']").on("change", function() {
        $.getJSON(
            "/task/taskAjaxSelectbyTaskGroup?id=" + $(this).val(),
            function(result) {
                if (initialChange) {
                    $('#task_type').parent().html(result[0]);
                    $('#priority').parent().html(result[1]);
                    $('#first_assignee_id').parent().html(result[2]);
                }
                initialChange = true;
                $('#tasktext').html(result[3]);
                $("#tasktext").fadeIn();
            }
        );
    });
    
    $("select[id='task_type']").on("change", function() {
        $.getJSON(
            "/task/ajaxGetTaskTypeFormFields?task_type=" + $("#task_type").val() + "&task_group_id=" + $("#task_group_id").val(),
            function(result) {
                if (result) {
                    $("#form_fields").html(result);
                    $("#form_fields").fadeIn();
                }
            }
        );
    });
    
    // Submit both forms 
    $("#edit_form, #form_fields_form").submit(function() {
        toggleModalLoading();
        var action = $(this).attr('action');
        $.ajax({
            url  : action,
            type : 'POST',
            data : $('#edit_form, #form_fields_form').serialize()
        });
        return true; 
    });
    
</script>