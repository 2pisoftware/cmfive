<?php echo $form; ?>

<script type="text/javascript">
    // Input values are module, search and description
    $(document).ready(function() {
        var searchBaseUrl = '/timelog/ajaxSearch';
        var searchUrl = searchBaseUrl;
        $("#object_class").change(function() {
            if ($(this).val() !== "") {
                $("#search").removeAttr("readonly");
                searchUrl = searchBaseUrl + "?index=" + $(this).val();
            } else {
                $("#search").attr("readonly", "true");
                searchUrl = searchBaseUrl;
            }
        });

        $("#search").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: searchUrl + "&term=" + request.term, 
                    success: function(result) {
                        response(JSON.parse(result));
                    }
                });
            },
            select: function(event, ui) {
                $("#object_id").val(ui.item.id);
				$.get('/timelog/ajaxGetExtraData/' + $("#object_class").val() + '/' + $("#object_id").val())
					.done(function(response) {
						var append_panel = "<div class='panel'><div class='row-fluid section-header'><h4>Additional Fields</h4></div><ul class='small-block-grid-1 medium-block-grid-1 section-body'><li>" + response + "</li></ul></div>";
						$("#timelog_edit_form .panel").after(append_panel);
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
    });
  
</script>