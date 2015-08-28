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
                $("#object_id").val(ui.item.value);
				$.get('/timelog/ajaxGetExtraDetails/<?php echo !empty($timelog_id) ? $timelog_id : ""; ?>', {
					success: function(response) {
						alert(response);
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
    });
  
</script>