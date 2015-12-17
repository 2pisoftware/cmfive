<?php echo $form; ?>

<script type="text/javascript">
    // Input values are module, search and description
    $(document).ready(function() {
        // If there is no task group selected, we disable submit
        if ($("#object_id").val() == '') {
            $(".savebutton").prop("disabled", true);
            $("#acp_search").attr("readonly", "true");
        }
        var searchBaseUrl = '/timelog/ajaxSearch';
        
        // If the start time changes and there is no end time then set end time
        // to start time, and vice versa
        $("#dt_start").focusout(function() {
            if ($("#dt_end").val() == "") {
                $('#dt_end').val($("#dt_start").val());
            }
            //console.log("Start has lost focus");
        });
        $("#dt_end").focusout(function() {
            if ($("#dt_start").val() == "") {
                $('#dt_start').val($("#dt_end").val());
            }
        });
        
        // If there is already a value in #object_class, that is, we are 
        // editing, then set the searchURL
        if ($("#object_class").val !== '') {
            searchUrl = searchBaseUrl + "?index=" + $(this).val();        
        }
        $("#object_class").change(function() {
            $("#acp_search").val('');
            $("#timelog_edit_form .panel + .panel").remove();
            if ($(this).val() !== "") {
                    $("#acp_search").removeAttr("readonly");
                    searchUrl = searchBaseUrl + "?index=" + $(this).val();
                } else {
                    // This fails with unknown page...
                    $("#acp_search").attr("readonly", "true");
                    searchUrl = searchBaseUrl;
                }
        });

        $("#acp_search").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: searchUrl + "&term=" + request.term, 
                    success: function(result) {
                        response(JSON.parse(result));
                    }
                });
            },
            // When the have selected a search value then do the ajax call  
            select: function(event, ui) {
                $("#object_id").val(ui.item.id);
                // Task is chosen, allow submit
                $(".savebutton").prop("disabled", false);
                $("#timelog_edit_form .panel + .panel").remove();
				$.get('/timelog/ajaxGetExtraData/' + $("#object_class").val() + '/' + $("#object_id").val())
					.done(function(response) {
                                            if (response != '') {
						var append_panel = "<div class='panel'><div class='row-fluid section-header'><h4>Additional Fields"+$("#object_class").val()+"</h4></div><ul class='small-block-grid-1 medium-block-grid-1 section-body'><li>" + response + "</li></ul></div>";
						$("#timelog_edit_form .panel").after(append_panel);
                                            }
					});
				
            },
            minLength: 3
        });
        
        $("#timelogForm").on("submit", function() {
            $.ajax({
                url: '/timelog/ajaxStart',
                method: 'POST',
                data: {
                    'object': $("#object_class").val(),
                    'object_id': $("#object_id").val(),
                    'description': $("#description").val()
                },
                success: function(result) {
                    alert(result);
                }
            });
            return false;
        });
        
        // Need to simulate change to module type to set url
        
    });
  
</script>