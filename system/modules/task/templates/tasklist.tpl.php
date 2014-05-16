<div class="tabs">
    <div class="tab-head">
        <a href="#list" class="active">Task List</a>
        <a href="#notifications">Notifications</a>
    </div>
    <div class="tab-body">
        <div id="list">
            <?php echo Html::filter("Search Tasks", $filter_data, "/task/tasklist"); ?>
            <form id="updatestatus" action="<?php echo $webroot . "/task/updatestatus"; ?>" method="POST">
                <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
                <?php echo $mytasks; ?>
            </form>
        </div>
        <div id="notifications" style="display:none;">
            Set up each of your Task Groups so that you will be notified, via your Inbox, of events that take place to Tasks relevant to you.
            <dl>
                <dt><b>Key</b>
                <dd><b>Creator</b>: Be notified of changes to Tasks that you have created. (All roles)
                <dd><b>Assignee</b>: Be notified of changes to Tasks assigned to you. (Members and Owners)
                <dd><b>All Others</b>: Be notified of changes to any Task within a Task Group. (Task Group Owners only)
            </dl>
            <p>
<?php echo $notify; ?>
        </div>
    </div>
</div>

<script language="javascript">
<?php
if (!empty($_REQUEST['tab'])) {
    echo "	switchTab(" . $_REQUEST['tab'] . ");";
}
?>

    var myFlag = true;

    $(document).ready(function() {
        $("select[id='assignee']").trigger("change");
    });

    $.ajaxSetup({
        cache: false
    });
    $(this).val("'" +<?php echo $w->Auth->user()->id; ?> + "'");
    $(".startTime").click(function(e) {
        var url = $(this).attr("href");
        var screenW = screen.width;
        var x = screenW - 360;
        var t = 0;
        var winName = "Task Time Log";
        var winParameters = "width=360,height=300,scrollbars=no,toolbar=no,status=no,menubar=no,location=no";

        var thiscookie = getCookie("thiswin");

        if (!thiscookie) {
            thiswin = window.open(url, winName, winParameters);
            thiswin.moveTo(x, t);
            thiswin.focus();
        }
        else {
            alert("Please END TIME on your current Task" + "\n" + "before starting a new Task Time Log");

            if (typeof(thiswin) != "undefined" && !thiswin.closed)
                thiswin.focus();
        }

        e.preventDefault();
    });

    $("#clrForm").click(function(e) {
        e.preventDefault();
        myFlag = false;
        $("select[id='assignee']").trigger("change");
    }
    );

    var task_url = "/task/taskAjaxSelectbyTaskGroup?id=";
    $("select[id='task_group_id'] option").click(function() {
        $.getJSON(
                task_url + $(this).val(),
                function(result) {
                    $('#task_type').parent().html(result[0]);
                    $('#priority').parent().html(result[1]);
                    $('#first_assignee_id').parent().html(result[2]);
                }
        );
    }
    );

    var list_url = "/task/taskAjaxAssigntoGroup?id=";
    $("select[id='assignee']").live("change", function() {
        $.getJSON(
                list_url + $(this).val(),
                function(result) {
                    $('#taskgroups').parent().html(result);
                    if (myFlag)
                        $("select#taskgroups").val("<?php echo!empty($reqTaskgroups) ? $reqTaskgroups : ''; ?>");
                    $("select[id='taskgroups']").trigger("change");
                }
        );
    }
    );

    var type_url = "/task/taskAjaxGrouptoType?id=";
    $("select[id='taskgroups']").live("change", function() {
        $.getJSON(
                type_url + $(this).val() + "_" + $("select[id='assignee']").val(),
                function(result) {
                    $('#tasktypes').parent().html(result);
                    if (myFlag)
                        $("select#tasktypes").val("<?php echo!empty($reqTasktypes) ? $reqTasktypes : ''; ?>");
                    $("select[id='tasktypes']").trigger("change");
                }
        );
    }
    );

    var priority_url = "/task/taskAjaxTypetoPriority?id=";
    $("select[id='tasktypes']").live("change", function() {
        $.getJSON(
                priority_url + $(this).val() + "_" + $("select[id='taskgroups']").val() + "_" + $("select[id='assignee']").val(),
                function(result) {
                    $('#tpriority').parent().html(result);
                    if (myFlag)
                        $("select#tpriority").val("<?php echo!empty($reqPriority) ? $reqPriority : ''; ?>");
                    $("select[id='tpriority']").trigger("change");
                }
        );
    }
    );

    var status_url = "/task/taskAjaxPrioritytoStatus?id=";
    $("select[id='tpriority']").live("change", function() {
        $.getJSON(
                status_url + $(this).val() + "_" + $("select[id='tasktypes']").val() + "_" + $("select[id='taskgroups']").val() + "_" + $("select[id='assignee']").val(),
                function(result) {
                    $('#status').parent().html(result);
                    if (myFlag)
                        $("select#status").val("<?php echo!empty($reqStatus) ? $reqStatus : ''; ?>");
                }
        );
    }
    );

    var update_url = "/task/updatestatus/";
    $("select:visible[id*='status_']").live("change", function() {
        var sid = $(this).attr("id");
        var s = sid.split("_");
        $('#updatestatus').attr("action", update_url + "?id=" + s[1] + "&status=" + $(this).val() + "&assignee=" +
                $("#leadfilter #assignee").val() + "&creator=" + $("#leadfilter #creator").val() + "&taskgroups=" +
                $("#leadfilter #taskgroups").val() + "&tasktypes=" + $("#leadfilter #tasktypes").val() + "&tpriority=" +
                $("#leadfilter #tpriority").val() + "&tstatus=" + $("#leadfilter #status").val() + "&dt_from=" +
                $("#leadfilter #dt_from").val() + "&dt_to=" + $("#leadfilter #dt_to").val());
        $('#updatestatus').submit();
    }
    );

    function getCookie(cname) {
        var cVal = null;
        if (document.cookie) {
            var arr = document.cookie.split((escape(cname) + '='));
            if (arr.length >= 2) {
                var arr2 = arr[1].split(';');
                cVal = unescape(arr2[0]);
            }
        }
        return cVal;
    }
</script>
